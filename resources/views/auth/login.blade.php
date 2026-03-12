@extends('layouts.store', ['title' => 'Вход'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Вход'],
    ]])

    <section class="auth-page">
        <div class="container">
            <div class="auth-page__card">
                <h2 class="auth-page__title">Вход в аккаунт</h2>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="auth-page__fields">
                        <div class="auth-page__field">
                            <label for="login" class="auth-page__label">Телефон, Telegram или Email</label>
                            <input type="text" id="login" name="login"
                                   value="{{ old('login') }}"
                                   class="auth-page__input {{ $errors->has('login') ? 'auth-page__input--error' : '' }}"
                                   required autofocus autocomplete="username"
                                   placeholder="+7... / @telegram / email">
                            @error('login')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password" class="auth-page__label">Пароль</label>
                            <input type="password" id="password" name="password"
                                   class="auth-page__input {{ $errors->has('password') ? 'auth-page__input--error' : '' }}"
                                   required autocomplete="current-password">
                            @error('password')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="auth-page__options">
                        <label class="auth-page__remember">
                            <input type="checkbox" name="remember">
                            <span>Запомнить меня</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-page__forgot">Забыли пароль?</a>
                        @endif
                    </div>

                    <button type="submit" class="auth-page__submit">Войти</button>
                </form>

                <p class="auth-page__alt">
                    Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
                </p>
            </div>
        </div>
    </section>
@endsection
