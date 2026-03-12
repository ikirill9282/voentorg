@extends('layouts.store', ['title' => 'Спасибо за заказ!', 'mainClass' => 'cart'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Оплата']]])

    <section class="page__wrapper">
        <div class="container">
            <div class="payment-result payment-result--success">
                <div class="payment-result__icon">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="30" stroke="#4CAF50" stroke-width="3" fill="none"/>
                        <path d="M20 33L28 41L44 23" stroke="#4CAF50" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <h2>Спасибо за заказ!</h2>
                <p class="payment-result__order">Заказ <strong>{{ $order->order_number }}</strong></p>
                <p class="payment-result__total">Сумма: <strong>{{ number_format($order->total, 0, '', ' ') }} ₽</strong></p>

                @if ($order->isPaid())
                    <p class="payment-result__status payment-result__status--paid">Оплачен</p>
                @else
                    <p class="payment-result__status payment-result__status--pending">Обрабатывается</p>
                @endif

                <p class="payment-result__info">Мы отправим подтверждение на <strong>{{ $order->customer_email }}</strong></p>

                <div class="payment-result__actions">
                    @auth
                        <a href="{{ route('account.orders') }}" class="basket__btn checkout-button wc-forward">Мои заказы</a>
                    @endauth
                    <a href="{{ route('store.home') }}" class="basket__btn checkout-button wc-forward" style="background:transparent;color:#898121;border:1px solid #898121;">На главную</a>
                </div>
            </div>
        </div>
    </section>
@endsection
