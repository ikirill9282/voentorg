<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCompany;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\Store;
use App\Models\UserAddress;
use App\Rules\YandexCaptcha;
use App\Services\BonusService;
use App\Services\CartService;
use App\Services\CdekService;
use App\Services\VtbAcquiringService;
use App\Services\YandexDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CdekService $cdekService,
        private readonly YandexDeliveryService $yandexDeliveryService,
        private readonly VtbAcquiringService $vtbService,
        private readonly BonusService $bonusService,
    ) {
    }

    public function create(): View
    {
        $cart = $this->cartService->summary();

        if ($cart['is_empty']) {
            return view('store.cart', compact('cart'));
        }

        $shippingMethods = ShippingMethod::query()
            ->active()
            ->orderBy('sort_order')
            ->get();

        $deliveryCompanies = DeliveryCompany::query()
            ->active()
            ->orderBy('sort_order')
            ->get();

        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $savedAddresses = collect();
        $bonusInfo = null;

        if ($user = auth()->user()) {
            $savedAddresses = $user->addresses()->with('shippingMethod')->get();
            if ((float) $user->bonus_balance > 0) {
                $bonusInfo = [
                    'balance' => (float) $user->bonus_balance,
                    'max_redeemable' => $this->bonusService->maxRedeemable($user, (float) $cart['total']),
                    'tier' => (int) $user->loyalty_tier,
                    'percentage' => $user->getLoyaltyPercentage(),
                ];
            }
        }

        return view('store.checkout', compact('cart', 'shippingMethods', 'deliveryCompanies', 'stores', 'savedAddresses', 'bonusInfo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->summary();

        if ($cart['is_empty']) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Корзина пуста.',
            ]);
        }

        $paymentMethods = array_keys($this->paymentMethods());

        $validated = $request->validate([
            'customer_first_name' => ['required', 'string', 'max:120'],
            'customer_last_name' => ['nullable', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['required', 'string', 'max:80'],
            'customer_address_line_1' => ['nullable', 'string', 'max:255'],
            'customer_address_line_2' => ['nullable', 'string', 'max:255'],
            'customer_city' => ['nullable', 'string', 'max:120'],
            'customer_region' => ['nullable', 'string', 'max:120'],
            'customer_postal_code' => ['nullable', 'string', 'max:40'],
            'customer_country' => ['nullable', 'string', 'max:3'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'delivery_company_id' => ['nullable', 'integer', 'exists:delivery_companies,id'],
            'delivery_region' => ['nullable', 'string', 'max:255'],
            'delivery_provider' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', $paymentMethods)],
            'comment' => ['nullable', 'string', 'max:2000'],
            'cdek_pvz_code' => ['nullable', 'string', 'max:50'],
            'cdek_tariff_id' => ['nullable', 'integer'],
            'cdek_delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'cdek_city_code' => ['nullable', 'string', 'max:20'],
            'pickup_store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'pickup_prepaid' => ['nullable', 'boolean'],
            'yandex_delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'bonus_amount' => ['nullable', 'numeric', 'min:0'],
            'saved_address_id' => ['nullable', 'integer'],
            'smart-token' => config('services.yandex_captcha.server_key') ? ['required', new YandexCaptcha] : [],
        ]);

        // If saved address selected, fill address fields from it
        if (! empty($validated['saved_address_id']) && auth()->check()) {
            $savedAddress = UserAddress::query()
                ->where('id', $validated['saved_address_id'])
                ->where('user_id', auth()->id())
                ->first();

            if ($savedAddress) {
                $validated['customer_city'] = $savedAddress->city;
                $validated['customer_postal_code'] = $savedAddress->postal_code;
                $validated['customer_address_line_1'] = $savedAddress->address_line_1;
                $validated['customer_address_line_2'] = $savedAddress->address_line_2;
                $validated['customer_region'] = $savedAddress->region;
            }
        }

        $shippingMethod = ShippingMethod::query()
            ->active()
            ->findOrFail($validated['shipping_method_id']);

        $isCdek = $shippingMethod->code === 'free_russia'
            && ! empty($validated['cdek_tariff_id']);

        $cdekOrder = null;
        $yandexOrder = null;
        $createdOrder = null;

        DB::transaction(function () use ($cart, $shippingMethod, $validated, $isCdek, &$cdekOrder, &$yandexOrder, &$createdOrder): void {
            $productIds = $cart['items']->pluck('product_id')->unique()->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $variantIds = $cart['items']->pluck('variant_id')->filter()->unique()->all();
            $variants = ! empty($variantIds)
                ? ProductVariant::query()
                    ->whereIn('id', $variantIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id')
                : collect();

            foreach ($cart['items'] as $item) {
                $product = $products->get((int) $item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Один из товаров недоступен.',
                    ]);
                }

                $variant = isset($item['variant_id']) ? $variants->get((int) $item['variant_id']) : null;

                $stock = $variant ? (int) $variant->stock : (int) $product->stock;

                if ($stock < (int) $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Недостаточно остатка для товара {$product->name}.",
                    ]);
                }
            }

            $subtotal = (float) $cart['total'];

            // Determine shipping cost based on method
            $deliveryProvider = $validated['delivery_provider'] ?? null;
            $shippingTotal = 0;

            if ($isCdek) {
                $shippingTotal = (float) ($validated['cdek_delivery_cost'] ?? 0);
                $deliveryProvider = 'cdek';
            } elseif ($shippingMethod->code === 'yandex' && ! empty($validated['yandex_delivery_cost'])) {
                $shippingTotal = (float) $validated['yandex_delivery_cost'];
                $deliveryProvider = 'yandex';
            } elseif (in_array($shippingMethod->code, ['ozon', 'pochta'])) {
                $shippingTotal = 0; // operator calculates
                $deliveryProvider = $shippingMethod->code;
            } elseif ($shippingMethod->code === 'pickup') {
                $shippingTotal = 0;
                $deliveryProvider = 'pickup';
            } elseif ($shippingMethod->code === 'free_moscow') {
                $deliveryProvider = 'moscow';
            } elseif ($shippingMethod->code === 'free_regions') {
                $deliveryProvider = 'regions';
            } else {
                $shippingTotal = (float) $shippingMethod->price;
            }

            $total = $subtotal + $shippingTotal;
            $discountAmount = (float) ($cart['discount'] ?? 0);

            // Increment coupon usage if applied
            if ($cart['coupon']) {
                $cart['coupon']->increment('used_count');
            }

            // Pickup: calculate estimated days
            $pickupEstimatedDays = null;
            $pickupStoreId = $validated['pickup_store_id'] ?? null;
            if ($shippingMethod->code === 'pickup' && $pickupStoreId) {
                $pickupEstimatedDays = $this->calculatePickupEstimatedDays($pickupStoreId, $cart['items']);
            }

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'customer_first_name' => $validated['customer_first_name'],
                'customer_last_name' => $validated['customer_last_name'] ?? '',
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address_line_1' => $validated['customer_address_line_1'] ?? '',
                'customer_address_line_2' => $validated['customer_address_line_2'] ?? null,
                'customer_city' => $validated['customer_city'] ?? '',
                'customer_region' => $validated['customer_region'] ?? null,
                'customer_postal_code' => $validated['customer_postal_code'] ?? null,
                'customer_country' => $validated['customer_country'] ?? 'RU',
                'shipping_method_id' => $shippingMethod->id,
                'delivery_company_id' => $validated['delivery_company_id'] ?? null,
                'delivery_region' => $validated['delivery_region'] ?? null,
                'delivery_provider' => $deliveryProvider,
                'pickup_store_id' => $pickupStoreId,
                'pickup_prepaid' => (bool) ($validated['pickup_prepaid'] ?? false),
                'pickup_estimated_days' => $pickupEstimatedDays,
                'subtotal' => $subtotal,
                'shipping_total' => $shippingTotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => Order::PAYMENT_PENDING,
                'status' => Order::STATUS_NEW,
                'comment' => $validated['comment'] ?? null,
            ]);

            foreach ($cart['items'] as $item) {
                $product = $products->get((int) $item['product_id']);
                $variant = isset($item['variant_id']) ? $variants->get((int) $item['variant_id']) : null;
                $quantity = (int) $item['quantity'];
                $price = $variant ? (float) $variant->price : (float) $product->price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $variant && $variant->sku ? $variant->sku : $product->sku,
                    'price' => $price,
                    'quantity' => $quantity,
                    'line_total' => $price * $quantity,
                ]);

                if ($variant) {
                    $variant->decrement('stock', $quantity);
                } else {
                    $product->decrement('stock', $quantity);
                }
            }

            // Create CDEK order if applicable
            if ($isCdek) {
                $cdekOrder = $order;
            }

            // Create Yandex delivery claim if applicable
            if ($shippingMethod->code === 'yandex') {
                $yandexOrder = $order;
            }

            // Bonus redemption
            $bonusUsed = 0;
            if (auth()->check() && ! empty($validated['bonus_amount']) && (float) $validated['bonus_amount'] > 0) {
                $user = auth()->user();
                $maxRedeemable = app(BonusService::class)->maxRedeemable($user, $total);
                $bonusUsed = min((float) $validated['bonus_amount'], $maxRedeemable);

                if ($bonusUsed > 0) {
                    $order->update([
                        'bonus_used' => $bonusUsed,
                        'total' => $total - $bonusUsed,
                    ]);
                    app(BonusService::class)->redeemForOrder($user, $order, $bonusUsed);
                }
            }

            $createdOrder = $order;
        });

        // Auto-save delivery address for authenticated users (max 3, no duplicates)
        if (auth()->check() && ! empty($validated['customer_address_line_1'])) {
            $user = auth()->user();
            $addressCount = $user->addresses()->count();

            if ($addressCount < 3) {
                $exists = $user->addresses()
                    ->where('address_line_1', $validated['customer_address_line_1'])
                    ->where('city', $validated['customer_city'] ?? '')
                    ->exists();

                if (! $exists) {
                    $user->addresses()->create([
                        'city' => $validated['customer_city'] ?? '',
                        'postal_code' => $validated['customer_postal_code'] ?? null,
                        'address_line_1' => $validated['customer_address_line_1'],
                        'address_line_2' => $validated['customer_address_line_2'] ?? null,
                        'region' => $validated['customer_region'] ?? null,
                        'shipping_method_id' => $validated['shipping_method_id'],
                    ]);
                }
            }
        }

        // Call CDEK API outside transaction (external HTTP call)
        if ($cdekOrder) {
            try {
                $cdekUuid = $this->cdekService->createOrder($cdekOrder, [
                    'pvz_code' => $validated['cdek_pvz_code'] ?? null,
                    'tariff_id' => $validated['cdek_tariff_id'] ?? null,
                    'city_code' => $validated['cdek_city_code'] ?? null,
                ]);

                if ($cdekUuid) {
                    $cdekOrder->update(['cdek_uuid' => $cdekUuid]);
                }
            } catch (\Throwable $e) {
                Log::error('CDEK order creation failed after checkout', [
                    'order' => $cdekOrder->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Call Yandex API outside transaction
        if ($yandexOrder && $this->yandexDeliveryService->isConfigured()) {
            try {
                $claimId = $this->yandexDeliveryService->createClaim($yandexOrder);
                if ($claimId) {
                    $yandexOrder->update(['yandex_claim_id' => $claimId]);
                }
            } catch (\Throwable $e) {
                Log::error('Yandex delivery claim creation failed', [
                    'order' => $yandexOrder->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->cartService->clear();

        // Online payment: register order in VTB and redirect to payment page
        if ($createdOrder && $validated['payment_method'] === 'online_payment' && $this->vtbService->isConfigured()) {
            $returnUrl = route('payment.success', ['orderId' => '__ORDER_ID__']);
            $failUrl = route('payment.fail', ['orderId' => '__ORDER_ID__']);

            $vtbResult = $this->vtbService->registerOrder($createdOrder, $returnUrl, $failUrl);

            if ($vtbResult) {
                $createdOrder->update([
                    'payment_id' => $vtbResult['orderId'],
                    'payment_url' => $vtbResult['formUrl'],
                ]);

                return redirect()->away($vtbResult['formUrl']);
            }

            // VTB registration failed — still created the order, redirect with warning
            Log::warning('VTB payment registration failed, order created without payment', [
                'order' => $createdOrder->order_number,
            ]);

            return redirect()->route('checkout.create')->with('success', 'Заказ создан, но не удалось перейти к оплате. Мы свяжемся с вами.');
        }

        return redirect()->route('checkout.create')->with('success', 'Заказ успешно создан! Мы свяжемся с вами для подтверждения.');
    }

    public function paymentSuccess(Request $request): View|RedirectResponse
    {
        $orderId = $request->query('orderId');

        if (! $orderId) {
            return redirect()->route('store.home');
        }

        $order = Order::query()->where('payment_id', $orderId)->first();

        if (! $order) {
            return redirect()->route('store.home');
        }

        // Check payment status with VTB
        if ($this->vtbService->isConfigured()) {
            $status = $this->vtbService->getOrderStatus($orderId);

            if ($status && (int) ($status['orderStatus'] ?? 0) === 2) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'paid_at' => now(),
                ]);
            }
        }

        return view('store.payment-success', compact('order'));
    }

    public function paymentFail(Request $request): View|RedirectResponse
    {
        $orderId = $request->query('orderId');

        if (! $orderId) {
            return redirect()->route('store.home');
        }

        $order = Order::query()->where('payment_id', $orderId)->first();

        if (! $order) {
            return redirect()->route('store.home');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_FAILED,
        ]);

        return view('store.payment-fail', compact('order'));
    }

    public function storeAvailability(Store $store): JsonResponse
    {
        $cart = $this->cartService->summary();

        if ($cart['is_empty']) {
            return response()->json(['items' => [], 'all_in_stock' => true]);
        }

        $productIds = $cart['items']->pluck('product_id')->unique()->all();

        $storeStock = DB::table('product_store_stock')
            ->where('store_id', $store->id)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        $items = [];
        $allInStock = true;

        foreach ($cart['items'] as $item) {
            $stock = $storeStock->get((int) $item['product_id']);
            $inStock = $stock && $stock->in_stock && $stock->quantity >= (int) $item['quantity'];

            if (! $inStock) {
                $allInStock = false;
            }

            $items[] = [
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'in_stock' => $inStock,
                'quantity' => $stock ? (int) $stock->quantity : 0,
            ];
        }

        return response()->json([
            'items' => $items,
            'all_in_stock' => $allInStock,
            'estimated_days' => $allInStock ? 0 : 5,
        ]);
    }

    public function yandexEstimate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:500'],
        ]);

        $cart = $this->cartService->summary();

        $items = [];
        foreach ($cart['items'] as $item) {
            $items[] = [
                'weight' => ($item['weight'] ?? 0.5) * 1000,
                'quantity' => $item['quantity'],
            ];
        }

        $result = $this->yandexDeliveryService->checkPrice($validated['address'], $items);

        if ($result && isset($result['price'])) {
            return response()->json([
                'success' => true,
                'price' => $result['price'],
                'currency' => $result['currency'] ?? 'RUB',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось рассчитать стоимость. Оператор уточнит стоимость после оформления.',
        ]);
    }

    private function calculatePickupEstimatedDays(int $storeId, $cartItems): int
    {
        $productIds = $cartItems->pluck('product_id')->unique()->all();

        $storeStock = DB::table('product_store_stock')
            ->where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->where('in_stock', true)
            ->pluck('product_id')
            ->all();

        foreach ($cartItems as $item) {
            if (! in_array((int) $item['product_id'], $storeStock)) {
                return 5; // delivery from factory
            }
        }

        return 0; // all in stock
    }

    private function paymentMethods(): array
    {
        return [
            'online_payment' => 'Оплатить онлайн',
            'bank_transfer' => 'Банковский перевод',
            'cash_on_delivery' => 'Оплата при получении',
            'invoice' => 'Счет для юр. лица',
        ];
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
