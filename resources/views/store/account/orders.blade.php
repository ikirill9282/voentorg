@extends('layouts.store', ['title' => 'Мои заказы', 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет', 'url' => route('account.dashboard')],
        ['title' => 'Мои заказы'],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Мои заказы</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    @forelse ($orders as $order)
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

                    @if ($orders->hasPages())
                        <div class="account__pagination">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
