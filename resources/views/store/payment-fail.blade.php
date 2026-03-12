@extends('layouts.store', ['title' => 'Оплата не прошла', 'mainClass' => 'cart'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Оплата']]])

    <section class="page__wrapper">
        <div class="container">
            <div class="payment-result payment-result--fail">
                <div class="payment-result__icon">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="30" stroke="#F44336" stroke-width="3" fill="none"/>
                        <path d="M22 22L42 42M42 22L22 42" stroke="#F44336" stroke-width="3" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
                <h2>Оплата не прошла</h2>
                <p class="payment-result__order">Заказ <strong>{{ $order->order_number }}</strong></p>
                <p class="payment-result__info">К сожалению, оплата не была завершена. Вы можете попробовать ещё раз или выбрать другой способ оплаты.</p>

                <div class="payment-result__actions">
                    @if ($order->payment_url)
                        <a href="{{ $order->payment_url }}" class="basket__btn checkout-button wc-forward">Попробовать снова</a>
                    @endif
                    <a href="{{ route('store.home') }}" class="basket__btn checkout-button wc-forward" style="background:transparent;color:#898121;border:1px solid #898121;">На главную</a>
                </div>
            </div>
        </div>
    </section>
@endsection
