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
