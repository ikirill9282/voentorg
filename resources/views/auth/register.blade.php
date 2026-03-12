@extends('layouts.store', ['title' => 'Регистрация'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Регистрация'],
    ]])

    <section class="auth-page">
        <div class="container">
            <div class="auth-page__card">
                <h2 class="auth-page__title">Регистрация в бонусной программе</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="auth-page__fields">
                        <div class="auth-page__row">
                            <div class="auth-page__field">
                                <label for="last_name" class="auth-page__label">Фамилия *</label>
                                <input type="text" id="last_name" name="last_name"
                                       value="{{ old('last_name') }}"
                                       class="auth-page__input {{ $errors->has('last_name') ? 'auth-page__input--error' : '' }}"
                                       required autofocus autocomplete="family-name">
                                @error('last_name')
                                    <span class="auth-page__error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="auth-page__field">
                                <label for="first_name" class="auth-page__label">Имя *</label>
                                <input type="text" id="first_name" name="first_name"
                                       value="{{ old('first_name') }}"
                                       class="auth-page__input {{ $errors->has('first_name') ? 'auth-page__input--error' : '' }}"
                                       required autocomplete="given-name">
                                @error('first_name')
                                    <span class="auth-page__error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="auth-page__row">
                            <div class="auth-page__field">
                                <label for="patronymic" class="auth-page__label">Отчество</label>
                                <input type="text" id="patronymic" name="patronymic"
                                       value="{{ old('patronymic') }}"
                                       class="auth-page__input"
                                       autocomplete="additional-name">
                            </div>
                            <div class="auth-page__field">
                                <label for="callsign" class="auth-page__label">Позывной</label>
                                <input type="text" id="callsign" name="callsign"
                                       value="{{ old('callsign') }}"
                                       class="auth-page__input">
                            </div>
                        </div>
                        <div class="auth-page__field">
                            <label for="birthday" class="auth-page__label">Дата рождения</label>
                            <input type="date" id="birthday" name="birthday"
                                   value="{{ old('birthday') }}"
                                   class="auth-page__input"
                                   max="{{ now()->subYear()->format('Y-m-d') }}">
                        </div>
                        <div class="auth-page__field">
                            <label for="telegram_username" class="auth-page__label">Тэг в Telegram <small>(рекомендовано)</small></label>
                            <input type="text" id="telegram_username" name="telegram_username"
                                   value="{{ old('telegram_username') }}"
                                   class="auth-page__input {{ $errors->has('telegram_username') ? 'auth-page__input--error' : '' }}"
                                   placeholder="@username"
                                   autocomplete="off">
                            <span class="auth-page__hint">Будет использоваться для оперативной связи</span>
                            @error('telegram_username')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="phone" class="auth-page__label">Номер телефона *</label>
                            <input type="tel" id="phone" name="phone"
                                   value="{{ old('phone') }}"
                                   class="auth-page__input {{ $errors->has('phone') ? 'auth-page__input--error' : '' }}"
                                   required autocomplete="tel">
                            @error('phone')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="email" class="auth-page__label">Почта <small>(необязательно)</small></label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="auth-page__input {{ $errors->has('email') ? 'auth-page__input--error' : '' }}"
                                   autocomplete="email">
                            @error('email')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password" class="auth-page__label">Пароль *</label>
                            <input type="password" id="password" name="password"
                                   class="auth-page__input {{ $errors->has('password') ? 'auth-page__input--error' : '' }}"
                                   required autocomplete="new-password">
                            @error('password')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password_confirmation" class="auth-page__label">Подтверждение пароля *</label>
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
