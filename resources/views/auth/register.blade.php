@extends('layouts.store', ['title' => 'Регистрация'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Регистрация'],
    ]])

    <section class="auth-page">
        <div class="container">
            <div class="auth-page__card">
                <h2 class="auth-page__title">Создать аккаунт</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="auth-page__fields">
                        <div class="auth-page__field">
                            <label for="name" class="auth-page__label">Имя</label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   class="auth-page__input {{ $errors->has('name') ? 'auth-page__input--error' : '' }}"
                                   required autofocus autocomplete="name">
                            @error('name')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="email" class="auth-page__label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="auth-page__input {{ $errors->has('email') ? 'auth-page__input--error' : '' }}"
                                   required autocomplete="username">
                            @error('email')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password" class="auth-page__label">Пароль</label>
                            <input type="password" id="password" name="password"
                                   class="auth-page__input {{ $errors->has('password') ? 'auth-page__input--error' : '' }}"
                                   required autocomplete="new-password">
                            @error('password')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password_confirmation" class="auth-page__label">Подтверждение пароля</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="auth-page__input"
                                   required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="auth-page__submit">Зарегистрироваться</button>
                </form>

                <p class="auth-page__alt">
                    Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
                </p>
            </div>
        </div>
    </section>
@endsection
