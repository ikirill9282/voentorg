<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CdekService
{
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->apiUrl = config('cdek.api_url');
        $this->clientId = config('cdek.client_id');
        $this->clientSecret = config('cdek.client_secret');
    }

    public function getToken(): ?string
    {
        return Cache::remember('cdek_auth_token', 3500, function () {
            $response = Http::asForm()->post("{$this->apiUrl}/oauth/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('CDEK auth failed', ['response' => $response->body()]);

            return null;
        });
    }

    public function proxyRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getToken();
        if (! $token) {
            return ['error' => 'Failed to authenticate with CDEK'];
        }

        $url = "{$this->apiUrl}/{$endpoint}";

        $request = Http::withToken($token)->acceptJson();

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            default => $request->get($url, $data),
        };

        return $response->json() ?? [];
    }

    public function calculateTariff(int $receiverCityCode, int $weight = 1000): array
    {
        $token = $this->getToken();
        if (! $token) {
            return [];
        }

        $response = Http::withToken($token)->post("{$this->apiUrl}/calculator/tarifflist", [
            'from_location' => [
                'code' => (int) config('cdek.sender_city_code'),
            ],
            'to_location' => [
                'code' => $receiverCityCode,
            ],
            'packages' => [
                [
                    'weight' => $weight,
                    'length' => 30,
                    'width' => 20,
                    'height' => 15,
                ],
            ],
        ]);

        if ($response->successful()) {
            return $response->json('tariff_codes', []);
        }

        Log::error('CDEK tariff calculation failed', ['response' => $response->body()]);

        return [];
    }

    public function createOrder(Order $order, array $cdekParams): ?string
    {
        $token = $this->getToken();
        if (! $token) {
            return null;
        }

        $packages = [];
        foreach ($order->items as $item) {
            $packages[] = [
                'number' => $order->order_number . '-' . $item->id,
                'weight' => 500,
                'length' => 20,
                'width' => 15,
                'height' => 10,
                'items' => [
                    [
                        'name' => $item->name,
                        'ware_key' => $item->sku ?? (string) $item->id,
                        'payment' => ['value' => 0],
                        'cost' => (float) $item->price,
                        'weight' => 500,
                        'amount' => $item->quantity,
                    ],
                ],
            ];
        }

        $payload = [
            'number' => $order->order_number,
            'tariff_code' => (int) ($cdekParams['tariff_id'] ?? 136),
            'sender' => [
                'name' => 'COLCHUGA',
                'phones' => [['number' => '+74998880701']],
            ],
            'recipient' => [
                'name' => $order->customer_first_name . ' ' . $order->customer_last_name,
                'phones' => [['number' => $order->customer_phone]],
                'email' => $order->customer_email,
            ],
            'from_location' => [
                'code' => (int) config('cdek.sender_city_code'),
                'address' => 'г. Москва, Остаповский проезд, дом 5, строение 10',
            ],
            'packages' => $packages,
        ];

        if (! empty($cdekParams['pvz_code'])) {
            $payload['delivery_point'] = $cdekParams['pvz_code'];
        } else {
            $payload['to_location'] = [
                'code' => (int) ($cdekParams['city_code'] ?? 0),
                'address' => $order->customer_address_line_1,
            ];
        }

        $response = Http::withToken($token)->post("{$this->apiUrl}/orders", $payload);

        if ($response->successful()) {
            $entity = $response->json('entity');

            return $entity['uuid'] ?? null;
        }

        Log::error('CDEK order creation failed', [
            'order' => $order->order_number,
            'response' => $response->body(),
        ]);

        return null;
    }
}
