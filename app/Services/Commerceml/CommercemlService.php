<?php

namespace App\Services\Commerceml;

use App\Models\CommercemlExchangeLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use ZipArchive;

class CommercemlService
{
    protected string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = config('commerceml.upload_dir');
    }

    protected function log(): \Psr\Log\LoggerInterface
    {
        return Log::channel('commerceml');
    }

    /**
     * Аутентификация 1С (Basic Auth) → возврат session cookie.
     */
    public function checkAuth(Request $request): Response
    {
        $username = $request->getUser() ?: $request->server('PHP_AUTH_USER');
        $password = $request->getPassword() ?: $request->server('PHP_AUTH_PW');

        if ($username !== config('commerceml.username') || $password !== config('commerceml.password')) {
            $this->log()->warning('CommerceML auth failed', [
                'username' => $username,
                'ip' => $request->ip(),
            ]);

            return response("failure\nInvalid credentials", 401);
        }

        $sessionName = config('session.cookie', 'laravel_session');
        $sessionId = Session::getId();

        $this->log()->info('CommerceML auth success', ['ip' => $request->ip()]);

        return response("success\n{$sessionName}\n{$sessionId}")
            ->withCookie(cookie($sessionName, $sessionId, config('session.lifetime')));
    }

    /**
     * Инициализация обмена — возврат параметров.
     */
    public function init(): Response
    {
        $zip = config('commerceml.zip_support') ? 'yes' : 'no';
        $limit = config('commerceml.file_limit');

        return response("zip={$zip}\nfile_limit={$limit}");
    }

    /**
     * Приём файлов от 1С (XML, изображения, ZIP).
     * 1С может отправлять файлы по частям (chunk upload).
     */
    public function uploadFile(Request $request, string $filename, string $type): Response
    {
        if (empty($filename)) {
            return response("failure\nFilename is required", 400);
        }

        $sessionDir = $this->getSessionDir();

        if (! is_dir($sessionDir)) {
            mkdir($sessionDir, 0755, true);
        }

        $filePath = $sessionDir . '/' . ltrim($filename, '/');
        $fileDir = dirname($filePath);

        if (! is_dir($fileDir)) {
            mkdir($fileDir, 0755, true);
        }

        // 1С отправляет содержимое файла в теле запроса
        $content = $request->getContent();
        file_put_contents($filePath, $content, FILE_APPEND);

        $this->log()->debug('CommerceML file uploaded', [
            'filename' => $filename,
            'type' => $type,
            'chunk_size' => strlen($content),
            'total_size' => filesize($filePath),
        ]);

        // Если это ZIP, распаковать
        if (str_ends_with(strtolower($filename), '.zip') && class_exists(ZipArchive::class)) {
            $this->extractZip($filePath, $sessionDir);
        }

        return response("success\n");
    }

    /**
     * Импорт загруженного файла (import.xml или offers.xml).
     */
    public function importFile(string $filename): Response
    {
        $sessionDir = $this->getSessionDir();
        $filePath = $sessionDir . '/' . ltrim($filename, '/');

        if (! file_exists($filePath)) {
            $this->log()->error('CommerceML import file not found', ['filename' => $filename]);
            return response("failure\nFile not found: {$filename}", 404);
        }

        // Блокировка для предотвращения параллельного импорта
        $lockFile = $this->uploadDir . '/.lock';
        $lock = fopen($lockFile, 'c');

        if (! flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);
            return response("progress\nAnother import is in progress, retry later");
        }

        try {
            $log = CommercemlExchangeLog::create([
                'type' => 'catalog',
                'mode' => 'import',
                'session_id' => Session::getId(),
                'filename' => $filename,
                'started_at' => now(),
            ]);

            if (str_contains($filename, 'import') && str_ends_with($filename, '.xml')) {
                $importer = new CatalogImporter($this->log());
                $stats = $importer->import($filePath, $sessionDir);
            } elseif (str_contains($filename, 'offers') && str_ends_with($filename, '.xml')) {
                $importer = new OffersImporter($this->log());
                $stats = $importer->import($filePath);
            } else {
                $this->log()->info('CommerceML skipping unknown file', ['filename' => $filename]);
                flock($lock, LOCK_UN);
                fclose($lock);

                return response("success\n");
            }

            $log->update([
                'status' => 'success',
                'products_created' => $stats['products_created'] ?? 0,
                'products_updated' => $stats['products_updated'] ?? 0,
                'categories_created' => $stats['categories_created'] ?? 0,
                'categories_updated' => $stats['categories_updated'] ?? 0,
                'completed_at' => now(),
            ]);

            $this->log()->info('CommerceML import completed', $stats);

            flock($lock, LOCK_UN);
            fclose($lock);

            return response("success\n");
        } catch (\Throwable $e) {
            $this->log()->error('CommerceML import failed', [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($log)) {
                $log->update([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);
            }

            flock($lock, LOCK_UN);
            fclose($lock);

            return response("failure\n" . $e->getMessage(), 500);
        }
    }

    /**
     * Выгрузка заказов для 1С (sale.query).
     */
    public function exportOrders(): Response
    {
        try {
            $exporter = new OrderExporter($this->log());
            $xml = $exporter->export();

            $log = CommercemlExchangeLog::create([
                'type' => 'sale',
                'mode' => 'query',
                'session_id' => Session::getId(),
                'status' => 'success',
                'orders_exported' => $exporter->getExportedCount(),
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            return response($xml, 200, ['Content-Type' => 'text/xml; charset=utf-8']);
        } catch (\Throwable $e) {
            $this->log()->error('CommerceML order export failed', [
                'error' => $e->getMessage(),
            ]);

            return response("failure\n" . $e->getMessage(), 500);
        }
    }

    /**
     * Подтверждение успешного получения заказов 1С (sale.success).
     */
    public function confirmOrderExchange(): Response
    {
        $this->log()->info('CommerceML order exchange confirmed by 1C');

        return response("success\n");
    }

    /**
     * Распаковка ZIP-архива.
     */
    protected function extractZip(string $zipPath, string $extractTo): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractTo);
            $zip->close();
            unlink($zipPath);

            $this->log()->debug('CommerceML ZIP extracted', ['path' => $zipPath]);
        } else {
            $this->log()->error('CommerceML ZIP extraction failed', ['path' => $zipPath]);
        }
    }

    /**
     * Путь к директории текущей сессии обмена.
     */
    protected function getSessionDir(): string
    {
        $sessionId = Session::getId() ?: 'default';

        return $this->uploadDir . '/' . $sessionId;
    }
}
