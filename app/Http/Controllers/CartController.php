<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function index(): View
    {
        $cart = $this->cartService->summary();

        return view('store.cart', compact('cart'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::query()
            ->active()
            ->with('images')
            ->findOrFail($validated['product_id']);

        $this->cartService->add(
            $product,
            (int) ($validated['quantity'] ?? 1),
            $validated['variant_id'] ?? null,
        );

        if ($request->expectsJson()) {
            $summary = $this->cartService->summary();

            return response()->json([
                'success' => true,
                'message' => 'Товар добавлен в корзину.',
                'cart_count' => $summary['total_quantity'],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Товар добавлен в корзину.');
    }

    public function update(Request $request, string $rowId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->cartService->update($rowId, (int) $validated['quantity']);

        return redirect()->route('cart.index')->with('success', 'Корзина обновлена.');
    }

    public function destroy(string $rowId): RedirectResponse
    {
        $this->cartService->remove($rowId);

        return redirect()->route('cart.index')->with('success', 'Товар удален из корзины.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')->with('success', 'Корзина очищена.');
    }

    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $summary = $this->cartService->summary();

        $this->cartService->applyCoupon($validated['coupon_code'], $summary['subtotal']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Купон применен.']);
        }

        return redirect()->route('cart.index')->with('success', 'Купон применен.');
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();

        return redirect()->route('cart.index')->with('success', 'Купон удален.');
    }

    public function exportPdf(): \Illuminate\Http\Response
    {
        $cart = $this->cartService->summary();

        $pdf = Pdf::loadView('store.cart-pdf', compact('cart'));

        return $pdf->download('cart-' . now()->format('Y-m-d') . '.pdf');
    }
}
