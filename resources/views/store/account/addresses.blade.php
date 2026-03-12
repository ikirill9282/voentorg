@extends('layouts.store', ['title' => 'Адреса доставки', 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет', 'url' => route('account.dashboard')],
        ['title' => 'Адреса доставки'],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Адреса доставки</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Existing addresses --}}
                    @foreach ($addresses as $address)
                        <div class="account__card address-card">
                            <div class="address-card__header">
                                <h3 class="account__card-title">{{ $address->label ?: 'Адрес ' . $loop->iteration }}</h3>
                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="address-card__delete" onclick="return confirm('Удалить адрес?')">Удалить</button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('account.addresses.update', $address) }}">
                                @csrf
                                @method('PATCH')
                                <div class="account__form-grid">
                                    <div class="account__field">
                                        <label class="account__label">Название</label>
                                        <input type="text" name="label" value="{{ $address->label }}" class="account__input" placeholder="Домашний, Рабочий...">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Город *</label>
                                        <input type="text" name="city" value="{{ $address->city }}" class="account__input" required>
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Индекс</label>
                                        <input type="text" name="postal_code" value="{{ $address->postal_code }}" class="account__input">
                                    </div>
                                    <div class="account__field" style="grid-column: 1 / -1;">
                                        <label class="account__label">Адрес *</label>
                                        <input type="text" name="address_line_1" value="{{ $address->address_line_1 }}" class="account__input" required>
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Адрес (доп.)</label>
                                        <input type="text" name="address_line_2" value="{{ $address->address_line_2 }}" class="account__input">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Регион</label>
                                        <input type="text" name="region" value="{{ $address->region }}" class="account__input">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Способ доставки</label>
                                        <select name="shipping_method_id" class="account__input">
                                            <option value="">Не выбран</option>
                                            @foreach ($shippingMethods as $method)
                                                <option value="{{ $method->id }}" {{ $address->shipping_method_id == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="account__btn" style="margin-top:10px;">Сохранить</button>
                            </form>
                        </div>
                    @endforeach

                    {{-- Add new address --}}
                    @if ($addresses->count() < 3)
                        <div class="account__card">
                            <h3 class="account__card-title">Добавить адрес</h3>
                            <form method="POST" action="{{ route('account.addresses.store') }}">
                                @csrf
                                <div class="account__form-grid">
                                    <div class="account__field">
                                        <label class="account__label">Название</label>
                                        <input type="text" name="label" class="account__input" placeholder="Домашний, Рабочий...">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Город *</label>
                                        <input type="text" name="city" class="account__input" required>
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Индекс</label>
                                        <input type="text" name="postal_code" class="account__input">
                                    </div>
                                    <div class="account__field" style="grid-column: 1 / -1;">
                                        <label class="account__label">Адрес *</label>
                                        <input type="text" name="address_line_1" class="account__input" required>
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Адрес (доп.)</label>
                                        <input type="text" name="address_line_2" class="account__input">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Регион</label>
                                        <input type="text" name="region" class="account__input">
                                    </div>
                                    <div class="account__field">
                                        <label class="account__label">Способ доставки</label>
                                        <select name="shipping_method_id" class="account__input">
                                            <option value="">Не выбран</option>
                                            @foreach ($shippingMethods as $method)
                                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="account__btn" style="margin-top:10px;">Добавить</button>
                            </form>
                        </div>
                    @else
                        <p style="color:#888; margin-top:15px;">Достигнут лимит: максимум 3 адреса.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
