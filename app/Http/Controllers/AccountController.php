<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\BonusService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private readonly BonusService $bonusService,
        private readonly QrCodeService $qrCodeService,
    ) {
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $recentOrders = $user->orders()
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $orderStats = [
            'total' => $user->orders()->count(),
            'active' => $user->orders()->whereIn('status', [Order::STATUS_NEW, Order::STATUS_PROCESSING])->count(),
            'completed' => $user->orders()->where('status', Order::STATUS_COMPLETED)->count(),
        ];

        $qrCode = $this->qrCodeService->generateForUser($user);

        $nextTierThreshold = $this->bonusService->nextTierThreshold((int) $user->loyalty_tier);

        return view('store.account.dashboard', compact('user', 'recentOrders', 'orderStats', 'qrCode', 'nextTierThreshold'));
    }

    public function orders(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('store.account.orders', compact('orders'));
    }

    public function orderShow(Request $request, Order $order): View
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load(['items.product', 'shippingMethod', 'deliveryCompany']);

        return view('store.account.order-show', compact('order'));
    }

    public function settings(Request $request): View
    {
        return view('store.account.settings', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Auto-fill name for backward compatibility
        $validated['name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('account.settings')->with('success', 'Профиль обновлён.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('account.settings')->with('success', 'Пароль изменён.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ==================== Addresses ====================

    public function addresses(Request $request): View
    {
        $addresses = $request->user()->addresses()->with('shippingMethod')->get();
        $shippingMethods = \App\Models\ShippingMethod::query()->active()->orderBy('sort_order')->get();

        return view('store.account.addresses', compact('addresses', 'shippingMethods'));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->addresses()->count() >= 3) {
            return redirect()->route('account.addresses')
                ->withErrors(['address' => 'Максимум 3 адреса доставки.']);
        }

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:120'],
            'shipping_method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
        ]);

        $user->addresses()->create($validated);

        return redirect()->route('account.addresses')->with('success', 'Адрес добавлен.');
    }

    public function updateAddress(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:120'],
            'shipping_method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
        ]);

        $address->update($validated);

        return redirect()->route('account.addresses')->with('success', 'Адрес обновлён.');
    }

    public function destroyAddress(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('account.addresses')->with('success', 'Адрес удалён.');
    }

    // ==================== Bonuses ====================

    public function bonusHistory(Request $request): View
    {
        $user = $request->user();
        $transactions = $user->bonusTransactions()
            ->with('order')
            ->latest()
            ->paginate(20);

        $qrCode = $this->qrCodeService->generateForUser($user);
        $nextTierThreshold = $this->bonusService->nextTierThreshold((int) $user->loyalty_tier);

        return view('store.account.bonuses', compact('user', 'transactions', 'qrCode', 'nextTierThreshold'));
    }
}
