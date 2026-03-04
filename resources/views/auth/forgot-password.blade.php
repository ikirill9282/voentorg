@extends('layouts.store', ['title' => 'Восстановление пароля'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Восстановление пароля'],
    ]])

    <section class="auth-page">
        <div class="container">
            <div class="auth-page__card">
                <h2 class="auth-page__title">Восстановление пароля</h2>
                <p class="auth-page__desc">Укажите ваш email, и мы отправим ссылку для сброса пароля.</p>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="auth-page__fields">
                        <div class="auth-page__field">
                            <label for="email" class="auth-page__label">Email</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="auth-page__input {{ $errors->has('email') ? 'auth-page__input--error' : '' }}"
                                   required autofocus>
                            @error('email')
                                <span class="auth-page__error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="auth-page__submit">Отправить ссылку</button>
                </form>

                <p class="auth-page__alt">
                    <a href="{{ route('login') }}">&larr; Вернуться к входу</a>
                </p>
            </div>
        </div>
    </section>
@endsection
