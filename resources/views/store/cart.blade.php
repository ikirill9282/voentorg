@extends('layouts.store', ['title' => 'Корзина', 'mainClass' => 'cart'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Корзина']]])

    <section class="page__wrapper">
        <div class="container"><h2 class="basket__title">Корзина</h2></div>

        @if ($cart['is_empty'])
            <div class="container">
                <p>Ваша корзина пуста</p>
                <a href="{{ route('shop.index') }}" class="basket__btn checkout-button wc-forward">Перейти в каталог</a>
            </div>
        @else
            <div class="container cart_inner">
                <div>
                    <div class="woocommerce">
                        <form class="woocommerce-cart-form" action="{{ route('cart.index') }}" method="post">
                            @csrf
                            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="product-name" data-title="Название">Название</th>
                                        <th data-title="Размер">Размер</th>
                                        <th data-title="Цвет">Цвет</th>
                                        <th class="product-quantity" data-title="Количество">Количество</th>
                                        <th data-title="Сумма">Сумма</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cart['items'] as $item)
                                        <tr class="woocommerce-cart-form__cart-item cart_item">
                                            <td class="product-name" data-title="Название">
                                                <div>
                                                    @if ($item['image'])
                                                        <img src="{{ $item['image'] }}" alt="product" width="75" class="cartimg">
                                                    @endif
                                                    <div class="cart-item-product-info">
                                                        <p><small>Артикул: {{ $item['sku'] ?? '' }}</small></p>
                                                        <a href="{{ route('shop.product', $item['slug']) }}">{{ $item['name'] }}</a>
                                                        @if (!empty($item['is_free_gift']))
                                                            <p style="color:#898121;font-weight:600;">Подарок</p>
                                                        @endif
                                                        @if (!empty($item['variant_attributes']))
                                                            @foreach ($item['variant_attributes'] as $attrName => $attrValue)
                                                                <p>{{ $attrName }}: {{ $attrValue }}</p>
                                                            @endforeach
                                                        @elseif ($item['variant_label'] ?? false)
                                                            <p>{{ $item['variant_label'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-title="Размер">
                                                @if ($item['size'] ?? false)
                                                    <div class="product_razmer">{{ $item['size'] }}</div>
                                                @endif
                                            </td>
                                            <td data-title="Цвет">{{ $item['color'] ?? '' }}</td>
                                            <td data-title="Количество">
                                                @if (!empty($item['is_free_gift']))
                                                    <span>1</span>
                                                @else
                                                    <div class="basket-product__btns__counter">
                                                        <span class="basket-product__btns__counter-decrease">-</span>
                                                        <div class="quantity">
                                                            <form method="POST" action="{{ route('cart.items.update', $item['row_id']) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="number" class="input-text qty text" name="quantity" value="{{ $item['quantity'] }}" min="1" step="1" inputmode="numeric" autocomplete="off">
                                                            </form>
                                                        </div>
                                                        <span class="basket-product__btns__counter-increase">+</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td data-title="Сумма" class="product-subtotal">
                                                @if (!empty($item['is_free_gift']))
                                                    <span class="woocommerce-Price-amount amount" style="color:#898121;"><bdi>Бесплатно</bdi></span>
                                                @else
                                                    <span class="woocommerce-Price-amount amount"><bdi>{{ number_format($item['line_total'], 0, '', ' ') }}<span class="woocommerce-Price-currencySymbol">₽</span></bdi></span>
                                                    <span class="peritem">{{ number_format($item['price'], 0, '', ' ') }}₽ / шт.</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($item['is_free_gift']))
                                                    <form method="POST" action="{{ route('cart.items.destroy', $item['row_id']) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="remove2" aria-label="Удалить товар">&#10005;</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="6" class="actions">
                                            <div class="coupon">
                                                <label for="coupon_code">Купон:</label>
                                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Код купона">
                                                <button type="button" class="button coupon-apply-btn" data-url="{{ route('cart.coupon.apply') }}">Применить купон</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div>
                    <div class="totals">
                        <h3>Ваша корзина</h3>
                        <div class="subtotal">
                            <p class="items-text">Товары ({{ $cart['total_quantity'] }})</p>
                            <p class="items-price">{{ number_format($cart['subtotal'], 0, '', ' ') }} ₽</p>
                        </div>
                        @if ($cart['total_weight'] > 0)
                            <div class="subtotal">
                                <p class="items-text">Общая масса</p>
                                <p class="items-price">{{ number_format($cart['total_weight'], 2, ',', ' ') }} кг</p>
                            </div>
                        @endif
                        <hr class="discount_hr" @if ($cart['discount'] <= 0) style="display:none;" @endif>
                        <div class="discount" @if ($cart['discount'] <= 0) style="display:none;" @endif>
                            <p class="items-text">Выгода</p>
                            <p class="items-price">-{{ number_format($cart['discount'], 0, '', ' ') }} ₽</p>
                        </div>
                        <hr>
                        <div>
                            <p class="total-text">Итого к оплате</p>
                            <p class="total-price">{{ number_format($cart['total'], 0, '', ' ') }} ₽</p>
                        </div>
                        <div class="coupon">
                            @if ($cart['coupon'])
                                <div class="coupon-applied">
                                    <span>Купон: <b>{{ $cart['coupon']->code }}</b></span>
                                    <form method="POST" action="{{ route('cart.coupon.remove') }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="coupon-remove">&#10005;</button>
                                    </form>
                                </div>
                            @else
                                <label for="coupon_code_1" class="screen-reader-text">Купон:</label>
                                <input type="text" name="coupon_code" class="input-text" id="coupon_code_1" value="" placeholder="Код купона">
                                <button type="button" class="button coupon-apply-btn-sidebar" data-url="{{ route('cart.coupon.apply') }}">Применить</button>
                                <p id="success" style="display:none;">Промокод успешно применен</p>
                                <p id="fail" style="display:none;">Промокод введён некорректно</p>
                                <p id="exist" style="display:none;">Промокод уже применен</p>
                            @endif
                        </div>
                        <a href="{{ route('checkout.create') }}" class="basket__btn checkout-button wc-forward">Перейти к оформлению</a>

                        <a href="{{ route('cart.pdf') }}" class="basket__btn reverse-color">Выгрузить в PDF</a>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
