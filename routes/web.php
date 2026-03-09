<?php

use App\Http\Controllers\Api\CdekController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('store.home');
Route::get('/shop', [StoreController::class, 'shop'])->name('shop.index');
Route::get('/product-category/{slug}', [StoreController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [StoreController::class, 'product'])->name('shop.product');
Route::get('/search', [StoreController::class, 'search'])->name('shop.search');
Route::get('/api/search', [StoreController::class, 'searchAjax'])->name('api.search');
Route::get('/nash-blog', [StoreController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [StoreController::class, 'blogShow'])->name('blog.show');
Route::get('/contacts', [StoreController::class, 'contacts'])->name('page.contacts');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/page/privacy-policy', [StoreController::class, 'privacyPolicy'])->name('page.privacy-policy');
Route::get('/policy', [StoreController::class, 'staticPage'])->defaults('slug', 'policy')->name('page.policy');
Route::get('/pravila-torgovli', [StoreController::class, 'staticPage'])->defaults('slug', 'pravila-torgovli')->name('page.pravila-torgovli');
Route::get('/sposob-dostavki', [StoreController::class, 'staticPage'])->defaults('slug', 'sposob-dostavki')->name('page.sposob-dostavki');
Route::get('/kak-sdelat-zakaz', [StoreController::class, 'staticPage'])->defaults('slug', 'kak-sdelat-zakaz')->name('page.kak-sdelat-zakaz');
Route::get('/sposoby-oplaty', [StoreController::class, 'staticPage'])->defaults('slug', 'sposoby-oplaty')->name('page.sposoby-oplaty');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{rowId}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{rowId}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/pdf', [CartController::class, 'exportPdf'])->name('cart.pdf');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/orders', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/api/products/{product}/variants', [ProductVariantController::class, 'show'])->name('api.product.variants');
Route::get('/api/products/{product}/variants/available', [ProductVariantController::class, 'available'])->name('api.product.variants.available');
Route::match(['get', 'post'], '/api/cdek/token', [CdekController::class, 'token'])->name('api.cdek.token');
Route::match(['get', 'post'], '/api/cdek/{endpoint}', [CdekController::class, 'proxy'])->where('endpoint', '.*')->name('api.cdek.proxy');
Route::get('/api/stores/{store}/cart-availability', [CheckoutController::class, 'storeAvailability'])->name('api.store.availability');
Route::post('/api/yandex-delivery/estimate', [CheckoutController::class, 'yandexEstimate'])->name('api.yandex.estimate');

Route::get('/dashboard', fn () => redirect()->route('account.dashboard'))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'orderShow'])->name('orders.show');
    Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
    Route::patch('/settings/profile', [AccountController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [AccountController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings', [AccountController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
