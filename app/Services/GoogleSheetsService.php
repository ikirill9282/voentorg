<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\ValueRange;

class GoogleSheetsService
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private string $sheetName;

    private int $headerRow;

    private int $dataStartRow;

    public function __construct()
    {
        $credentialsPath = config('google.service_account_json');

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(Sheets::SPREADSHEETS);

        $this->sheets = new Sheets($client);
        $this->spreadsheetId = config('google.spreadsheet_id');
        $this->sheetName = config('google.sheet_name');
        $this->headerRow = config('google.header_row', 3);
        $this->dataStartRow = config('google.data_start_row', 4);
    }

    public function readHeaders(): array
    {
        $range = $this->range("{$this->headerRow}:{$this->headerRow}");

        $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);

        return $response->getValues()[0] ?? [];
    }

    public function readAllRows(): array
    {
        $range = $this->range("{$this->dataStartRow}:99999");

        $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);

        return $response->getValues() ?? [];
    }

    public function writeAllRows(array $header, array $rows): int
    {
        // Clear everything from header row onwards
        $this->sheets->spreadsheets_values->clear(
            $this->spreadsheetId,
            $this->range("{$this->headerRow}:99999"),
            new ClearValuesRequest()
        );

        // Prepare data: header + rows
        $allData = array_merge([$header], $rows);

        $body = new ValueRange(['values' => $allData]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $this->range("A{$this->headerRow}"),
            $body,
            ['valueInputOption' => 'RAW']
        );

        return count($rows);
    }

    public function updateRow(int $rowNumber, array $values): void
    {
        $body = new ValueRange(['values' => [$values]]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $this->range("A{$rowNumber}"),
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    public function appendRows(array $rows): void
    {
        $body = new ValueRange(['values' => $rows]);

        $this->sheets->spreadsheets_values->append(
            $this->spreadsheetId,
            $this->range("A{$this->dataStartRow}"),
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    private function range(string $range): string
    {
        return "'{$this->sheetName}'!{$range}";
    }
}
