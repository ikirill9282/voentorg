@extends('layouts.store', ['title' => 'Новый пароль'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Новый пароль'],
    ]])

    <section class="auth-page">
        <div class="container">
            <div class="auth-page__card">
                <h2 class="auth-page__title">Новый пароль</h2>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="auth-page__fields">
                        <div class="auth-page__field">
                            <label for="email" class="auth-page__label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $request->email) }}"
                                   class="auth-page__input {{ $errors->has('email') ? 'auth-page__input--error' : '' }}"
                                   required autofocus autocomplete="username">
                            @error('email')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-page__field">
                            <label for="password" class="auth-page__label">Новый пароль</label>
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

                    <button type="submit" class="auth-page__submit">Сбросить пароль</button>
                </form>
            </div>
        </div>
    </section>
@endsection
