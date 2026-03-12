<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YandexDeliveryService
{
    private string $apiUrl;
    private string $token;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('yandex_delivery.api_url'), '/');
        $this->token = config('yandex_delivery.token');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->token);
    }

    public function checkPrice(string $toAddress, array $items = []): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $totalWeight = 0;
        foreach ($items as $item) {
            $totalWeight += ($item['weight'] ?? 500) * ($item['quantity'] ?? 1);
        }

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post("{$this->apiUrl}/b2b/cargo/integration/v2/check-price", [
                    'items' => [[
                        'quantity' => 1,
                        'size' => ['height' => 0.2, 'length' => 0.3, 'width' => 0.2],
                        'weight' => max($totalWeight / 1000, 0.5),
                    ]],
                    'route_points' => [
                        [
                            'fullname' => config('yandex_delivery.sender_address'),
                            'type' => 'source',
                        ],
                        [
                            'fullname' => $toAddress,
                            'type' => 'destination',
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Yandex delivery check-price failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Yandex delivery check-price error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function createClaim(Order $order): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'title' => $item->name,
                'quantity' => $item->quantity,
                'cost_value' => (string) round($item->price),
                'cost_currency' => 'RUB',
                'size' => ['height' => 0.15, 'length' => 0.2, 'width' => 0.1],
                'weight' => 0.5,
            ];
        }

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post("{$this->apiUrl}/b2b/cargo/integration/v2/claims/create", [
                    'items' => $items,
                    'route_points' => [
                        [
                            'type' => 'source',
                            'point_id' => 1,
                            'visit_order' => 1,
                            'contact' => [
                                'name' => 'COLCHUGA',
                                'phone' => config('yandex_delivery.sender_phone'),
                            ],
                            'address' => [
                                'fullname' => config('yandex_delivery.sender_address'),
                            ],
                        ],
                        [
                            'type' => 'destination',
                            'point_id' => 2,
                            'visit_order' => 2,
                            'contact' => [
                                'name' => trim($order->customer_first_name . ' ' . $order->customer_last_name),
                                'phone' => $order->customer_phone,
                            ],
                            'address' => [
                                'fullname' => trim($order->customer_city . ', ' . $order->customer_address_line_1),
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['id'] ?? null;
            }

            Log::warning('Yandex delivery create claim failed', [
                'order' => $order->order_number,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Yandex delivery create claim error', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
