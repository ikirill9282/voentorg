@extends('layouts.store', ['title' => 'Бонусная программа', 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет', 'url' => route('account.dashboard')],
        ['title' => 'Бонусная программа'],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Бонусная программа</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    {{-- Loyalty card --}}
                    <div class="loyalty-card">
                        <div class="loyalty-card__info">
                            <div class="loyalty-card__balance">
                                <span class="loyalty-card__balance-label">Баланс бонусов</span>
                                <span class="loyalty-card__balance-value">{{ number_format($user->bonus_balance, 0, '', ' ') }} &#8381;</span>
                            </div>
                            <div class="loyalty-card__tier">
                                <span class="loyalty-card__tier-badge loyalty-card__tier-badge--{{ $user->loyalty_tier }}">{{ $user->getLoyaltyPercentage() }}%</span>
                                <span class="loyalty-card__tier-label">Кешбэк</span>
                            </div>
                            <div class="loyalty-card__spent">
                                <span class="loyalty-card__spent-label">Всего покупок</span>
                                <span class="loyalty-card__spent-value">{{ number_format($user->total_spent, 0, '', ' ') }} &#8381;</span>
                            </div>
                        </div>

                        @if ($nextTierThreshold)
                            <div class="loyalty-card__progress">
                                @php
                                    $currentThresholds = [0, 50000, 100000, 200000];
                                    $currentMin = $currentThresholds[$user->loyalty_tier - 1] ?? 0;
                                    $progress = $nextTierThreshold > $currentMin
                                        ? min(100, (($user->total_spent - $currentMin) / ($nextTierThreshold - $currentMin)) * 100)
                                        : 100;
                                @endphp
                                <div class="loyalty-card__progress-bar">
                                    <div class="loyalty-card__progress-fill" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="loyalty-card__progress-text">
                                    До следующего уровня: {{ number_format($nextTierThreshold - $user->total_spent, 0, '', ' ') }} &#8381;
                                </span>
                            </div>
                        @else
                            <div class="loyalty-card__progress">
                                <span class="loyalty-card__progress-text">Максимальный уровень достигнут!</span>
                            </div>
                        @endif

                        <div class="loyalty-card__qr">
                            <p class="loyalty-card__qr-label">Ваш QR-код для магазинов</p>
                            <div class="loyalty-card__qr-image">{!! $qrCode !!}</div>
                        </div>
                    </div>

                    {{-- Tier info --}}
                    <div class="account__card">
                        <h3 class="account__card-title">Уровни программы</h3>
                        <div class="loyalty-tiers">
                            <div class="loyalty-tier {{ $user->loyalty_tier >= 1 ? 'loyalty-tier--active' : '' }}">
                                <span class="loyalty-tier__percent">5%</span>
                                <span class="loyalty-tier__range">до 50 000 &#8381;</span>
                            </div>
                            <div class="loyalty-tier {{ $user->loyalty_tier >= 2 ? 'loyalty-tier--active' : '' }}">
                                <span class="loyalty-tier__percent">7%</span>
                                <span class="loyalty-tier__range">50 000 — 100 000 &#8381;</span>
                            </div>
                            <div class="loyalty-tier {{ $user->loyalty_tier >= 3 ? 'loyalty-tier--active' : '' }}">
                                <span class="loyalty-tier__percent">10%</span>
                                <span class="loyalty-tier__range">100 000 — 200 000 &#8381;</span>
                            </div>
                            <div class="loyalty-tier {{ $user->loyalty_tier >= 4 ? 'loyalty-tier--active' : '' }}">
                                <span class="loyalty-tier__percent">15%</span>
                                <span class="loyalty-tier__range">от 200 000 &#8381;</span>
                            </div>
                        </div>
                        <p style="font-size:13px;color:#888;margin-top:10px;">Бонусами можно оплатить до 50% заказа.</p>
                    </div>

                    {{-- Transaction history --}}
                    <div class="account__section">
                        <h3>История операций</h3>
                        @if ($transactions->count())
                            <div class="bonus-history">
                                @foreach ($transactions as $tx)
                                    <div class="bonus-history__row">
                                        <div class="bonus-history__date">{{ $tx->created_at->format('d.m.Y H:i') }}</div>
                                        <div class="bonus-history__desc">
                                            {{ $tx->description }}
                                            @if ($tx->order)
                                                <small>(#{{ $tx->order->order_number }})</small>
                                            @endif
                                        </div>
                                        <div class="bonus-history__amount {{ (float) $tx->amount >= 0 ? 'bonus-history__amount--plus' : 'bonus-history__amount--minus' }}">
                                            {{ (float) $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount, 0, '', ' ') }} &#8381;
                                        </div>
                                        <div class="bonus-history__balance">{{ number_format($tx->balance_after, 0, '', ' ') }} &#8381;</div>
                                    </div>
                                @endforeach
                            </div>
                            {{ $transactions->links() }}
                        @else
                            <p style="color:#888;">Пока нет операций.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
