<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VtbAcquiringService
{
    private string $apiUrl;

    private string $login;

    private string $password;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('vtb_acquiring.api_url', ''), '/');
        $this->login = config('vtb_acquiring.merchant_login', '');
        $this->password = config('vtb_acquiring.merchant_password', '');
    }

    public function isConfigured(): bool
    {
        return $this->login !== '' && $this->password !== '' && $this->apiUrl !== '';
    }

    /**
     * Регистрация заказа в ВТБ.
     *
     * @return array{orderId: string, formUrl: string}|null
     */
    public function registerOrder(Order $order, string $returnUrl, string $failUrl): ?array
    {
        try {
            $response = Http::asForm()->post("{$this->apiUrl}/register.do", [
                'userName' => $this->login,
                'password' => $this->password,
                'orderNumber' => $order->order_number,
                'amount' => (int) round((float) $order->total * 100),
                'returnUrl' => $returnUrl,
                'failUrl' => $failUrl,
                'description' => "Заказ {$order->order_number}",
            ]);

            $data = $response->json();

            if (isset($data['orderId'], $data['formUrl'])) {
                return [
                    'orderId' => $data['orderId'],
                    'formUrl' => $data['formUrl'],
                ];
            }

            Log::error('VTB register.do error', [
                'order' => $order->order_number,
                'response' => $data,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('VTB register.do exception', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Проверка статуса оплаты.
     *
     * orderStatus: 0 — заказ зарегистрирован, 1 — предавторизация, 2 — оплачен, 3 — отменён, 4 — возврат, 6 — авторизация отклонена
     *
     * @return array|null
     */
    public function getOrderStatus(string $paymentId): ?array
    {
        try {
            $response = Http::asForm()->post("{$this->apiUrl}/getOrderStatusExtended.do", [
                'userName' => $this->login,
                'password' => $this->password,
                'orderId' => $paymentId,
            ]);

            $data = $response->json();

            if (isset($data['orderStatus'])) {
                return $data;
            }

            Log::error('VTB getOrderStatusExtended.do error', [
                'paymentId' => $paymentId,
                'response' => $data,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('VTB getOrderStatusExtended.do exception', [
                'paymentId' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
