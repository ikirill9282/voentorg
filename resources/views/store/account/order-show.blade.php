@extends('layouts.store', ['title' => 'Заказ #' . $order->order_number, 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет', 'url' => route('account.dashboard')],
        ['title' => 'Мои заказы', 'url' => route('account.orders')],
        ['title' => '#' . $order->order_number],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Заказ #{{ $order->order_number }}</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    {{-- Order summary --}}
                    <div class="order-detail__header">
                        <div class="order-detail__meta">
                            <span class="order-detail__date">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            <span class="account__order-status account__order-status--{{ $order->status }}">
                                @switch($order->status)
                                    @case('new') Новый @break
                                    @case('processing') В обработке @break
                                    @case('completed') Выполнен @break
                                    @case('cancelled') Отменён @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                            <span class="order-detail__payment order-detail__payment--{{ $order->payment_status }}">
                                @switch($order->payment_status)
                                    @case('pending') Ожидает оплаты @break
                                    @case('paid') Оплачен @break
                                    @case('failed') Ошибка оплаты @break
                                    @default {{ $order->payment_status }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="order-detail__section">
                        <h4 class="order-detail__section-title">Товары</h4>
                        <div class="order-detail__items">
                            @foreach ($order->items as $item)
                                <div class="order-detail__item">
                                    @if ($item->product && $item->product->image)
                                        <img src="{{ $item->product->image }}" alt="{{ $item->name }}" class="order-detail__item-img">
                                    @else
                                        <div class="order-detail__item-img order-detail__item-img--placeholder"></div>
                                    @endif
                                    <div class="order-detail__item-info">
                                        <span class="order-detail__item-name">{{ $item->name }}</span>
                                        @if ($item->sku)
                                            <span class="order-detail__item-sku">Арт: {{ $item->sku }}</span>
                                        @endif
                                    </div>
                                    <span class="order-detail__item-qty">x{{ $item->quantity }}</span>
                                    <span class="order-detail__item-price">{{ number_format($item->line_total, 0, '', ' ') }} &#8381;</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="order-detail__totals">
                        <div class="order-detail__total-row">
                            <span>Подитог</span>
                            <span>{{ number_format($order->subtotal, 0, '', ' ') }} &#8381;</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="order-detail__total-row">
                                <span>Скидка</span>
                                <span>-{{ number_format($order->discount_amount, 0, '', ' ') }} &#8381;</span>
                            </div>
                        @endif
                        <div class="order-detail__total-row">
                            <span>Доставка</span>
                            <span>{{ $order->shipping_total > 0 ? number_format($order->shipping_total, 0, '', ' ') . ' ₽' : 'Бесплатно' }}</span>
                        </div>
                        <div class="order-detail__total-row order-detail__total-row--final">
                            <span>Итого</span>
                            <span>{{ number_format($order->total, 0, '', ' ') }} &#8381;</span>
                        </div>
                    </div>

                    {{-- Delivery info --}}
                    <div class="order-detail__section">
                        <h4 class="order-detail__section-title">Доставка</h4>
                        <div class="order-detail__info-grid">
                            <div>
                                <span class="order-detail__label">Получатель</span>
                                <span class="order-detail__value">{{ $order->customer_first_name }} {{ $order->customer_last_name }}</span>
                            </div>
                            <div>
                                <span class="order-detail__label">Телефон</span>
                                <span class="order-detail__value">{{ $order->customer_phone }}</span>
                            </div>
                            <div>
                                <span class="order-detail__label">Email</span>
                                <span class="order-detail__value">{{ $order->customer_email }}</span>
                            </div>
                            @if ($order->customer_city)
                                <div>
                                    <span class="order-detail__label">Город</span>
                                    <span class="order-detail__value">{{ $order->customer_city }}</span>
                                </div>
                            @endif
                            @if ($order->customer_address_line_1)
                                <div>
                                    <span class="order-detail__label">Адрес</span>
                                    <span class="order-detail__value">{{ $order->customer_address_line_1 }}{{ $order->customer_address_line_2 ? ', ' . $order->customer_address_line_2 : '' }}</span>
                                </div>
                            @endif
                            @if ($order->shippingMethod)
                                <div>
                                    <span class="order-detail__label">Способ доставки</span>
                                    <span class="order-detail__value">{{ $order->shippingMethod->name }}</span>
                                </div>
                            @endif
                            @if ($order->cdek_tracking_number)
                                <div>
                                    <span class="order-detail__label">Трек-номер СДЭК</span>
                                    <span class="order-detail__value">{{ $order->cdek_tracking_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($order->comment)
                        <div class="order-detail__section">
                            <h4 class="order-detail__section-title">Комментарий</h4>
                            <p class="order-detail__comment">{{ $order->comment }}</p>
                        </div>
                    @endif

                    <a href="{{ route('account.orders') }}" class="account__link">&larr; Назад к заказам</a>
                </div>
            </div>
        </div>
    </section>
@endsection
