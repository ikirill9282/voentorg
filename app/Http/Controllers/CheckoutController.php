<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCompany;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CdekService;
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

        return view('store.checkout', compact('cart', 'shippingMethods', 'deliveryCompanies'));
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
            'payment_method' => ['required', 'string', 'in:' . implode(',', $paymentMethods)],
            'comment' => ['nullable', 'string', 'max:2000'],
            'cdek_pvz_code' => ['nullable', 'string', 'max:50'],
            'cdek_tariff_id' => ['nullable', 'integer'],
            'cdek_delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'cdek_city_code' => ['nullable', 'string', 'max:20'],
        ]);

        $shippingMethod = ShippingMethod::query()
            ->active()
            ->findOrFail($validated['shipping_method_id']);

        $isCdek = $shippingMethod->code === 'free_russia'
            && ! empty($validated['cdek_tariff_id']);

        $cdekOrder = null;

        DB::transaction(function () use ($cart, $shippingMethod, $validated, $isCdek, &$cdekOrder): void {
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
            $shippingTotal = $isCdek
                ? (float) ($validated['cdek_delivery_cost'] ?? 0)
                : (float) $shippingMethod->price;
            $total = $subtotal + $shippingTotal;
            $discountAmount = (float) ($cart['discount'] ?? 0);

            // Increment coupon usage if applied
            if ($cart['coupon']) {
                $cart['coupon']->increment('used_count');
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
        });

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

        $this->cartService->clear();

        return redirect()->route('checkout.create')->with('success', 'Заказ успешно создан! Мы свяжемся с вами для подтверждения.');
    }

    private function paymentMethods(): array
    {
        return [
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
