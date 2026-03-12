@extends('layouts.store', ['title' => 'Личный кабинет', 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет'],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Личный кабинет</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    {{-- Loyalty card --}}
                    <div class="loyalty-card loyalty-card--compact">
                        <div class="loyalty-card__info">
                            <div class="loyalty-card__balance">
                                <span class="loyalty-card__balance-label">Баланс бонусов</span>
                                <span class="loyalty-card__balance-value">{{ number_format($user->bonus_balance, 0, '', ' ') }} &#8381;</span>
                            </div>
                            <div class="loyalty-card__tier">
                                <span class="loyalty-card__tier-badge loyalty-card__tier-badge--{{ $user->loyalty_tier }}">{{ $user->getLoyaltyPercentage() }}%</span>
                                <span class="loyalty-card__tier-label">Кешбэк</span>
                            </div>
                            <div class="loyalty-card__qr loyalty-card__qr--small">
                                <div class="loyalty-card__qr-image">{!! $qrCode !!}</div>
                            </div>
                        </div>
                        @if ($nextTierThreshold)
                            @php
                                $currentThresholds = [0, 50000, 100000, 200000];
                                $currentMin = $currentThresholds[$user->loyalty_tier - 1] ?? 0;
                                $progress = $nextTierThreshold > $currentMin
                                    ? min(100, (($user->total_spent - $currentMin) / ($nextTierThreshold - $currentMin)) * 100)
                                    : 100;
                            @endphp
                            <div class="loyalty-card__progress">
                                <div class="loyalty-card__progress-bar">
                                    <div class="loyalty-card__progress-fill" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="loyalty-card__progress-text">
                                    До следующего уровня: {{ number_format($nextTierThreshold - $user->total_spent, 0, '', ' ') }} &#8381;
                                </span>
                            </div>
                        @endif
                        <a href="{{ route('account.bonuses') }}" class="loyalty-card__link">Подробнее &rarr;</a>
                    </div>

                    <div class="account__stats">
                        <div class="account__stat-card">
                            <span class="account__stat-number">{{ $orderStats['total'] }}</span>
                            <span class="account__stat-label">Всего заказов</span>
                        </div>
                        <div class="account__stat-card">
                            <span class="account__stat-number">{{ $orderStats['active'] }}</span>
                            <span class="account__stat-label">Активные</span>
                        </div>
                        <div class="account__stat-card">
                            <span class="account__stat-number">{{ $orderStats['completed'] }}</span>
                            <span class="account__stat-label">Выполненные</span>
                        </div>
                    </div>

                    <div class="account__section">
                        <div class="account__section-header">
                            <h3>Последние заказы</h3>
                            @if ($recentOrders->count())
                                <a href="{{ route('account.orders') }}" class="account__link">Все заказы &rarr;</a>
                            @endif
                        </div>

                        @forelse ($recentOrders as $order)
                            <a href="{{ route('account.orders.show', $order) }}" class="account__order-row">
                                <span class="account__order-number">#{{ $order->order_number }}</span>
                                <span class="account__order-date">{{ $order->created_at->format('d.m.Y') }}</span>
                                <span class="account__order-items">{{ $order->items->count() }} {{ trans_choice('товар|товара|товаров', $order->items->count()) }}</span>
                                <span class="account__order-total">{{ number_format($order->total, 0, '', ' ') }} &#8381;</span>
                                <span class="account__order-status account__order-status--{{ $order->status }}">
                                    @switch($order->status)
                                        @case('new') Новый @break
                                        @case('processing') В обработке @break
                                        @case('completed') Выполнен @break
                                        @case('cancelled') Отменён @break
                                        @default {{ $order->status }}
                                    @endswitch
                                </span>
                            </a>
                        @empty
                            <div class="account__empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z" fill="#ccc"/>
                                </svg>
                                <p>У вас пока нет заказов</p>
                                <a href="{{ route('shop.index') }}" class="account__btn">Перейти в каталог</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
