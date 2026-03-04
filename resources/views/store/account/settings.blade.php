@extends('layouts.store', ['title' => 'Настройки', 'mainClass' => 'account-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Личный кабинет', 'url' => route('account.dashboard')],
        ['title' => 'Настройки'],
    ]])

    <section class="account">
        <div class="container">
            <h2 class="account__title">Настройки</h2>
            <div class="account__layout">
                @include('store.account.partials.sidebar')

                <div class="account__content">
                    {{-- Profile form --}}
                    <div class="account__card">
                        <h3 class="account__card-title">Личные данные</h3>
                        <form method="POST" action="{{ route('account.settings.profile') }}">
                            @csrf
                            @method('PATCH')
                            <div class="account__form-grid">
                                <div class="account__field">
                                    <label for="name" class="account__label">Имя</label>
                                    <input type="text" id="name" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="account__input {{ $errors->has('name') ? 'account__input--error' : '' }}"
                                           required>
                                    @error('name')
                                        <span class="account__field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="account__field">
                                    <label for="email" class="account__label">Email</label>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="account__input {{ $errors->has('email') ? 'account__input--error' : '' }}"
                                           required>
                                    @error('email')
                                        <span class="account__field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="account__btn">Сохранить</button>
                        </form>
                    </div>

                    {{-- Password form --}}
                    <div class="account__card">
                        <h3 class="account__card-title">Сменить пароль</h3>
                        <form method="POST" action="{{ route('account.settings.password') }}">
                            @csrf
                            @method('PUT')
                            <div class="account__form-grid">
                                <div class="account__field">
                                    <label for="current_password" class="account__label">Текущий пароль</label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="account__input {{ $errors->updatePassword->has('current_password') ? 'account__input--error' : '' }}"
                                           required>
                                    @if ($errors->updatePassword->has('current_password'))
                                        <span class="account__field-error">{{ $errors->updatePassword->first('current_password') }}</span>
                                    @endif
                                </div>
                                <div class="account__field">
                                    <label for="password" class="account__label">Новый пароль</label>
                                    <input type="password" id="password" name="password"
                                           class="account__input {{ $errors->updatePassword->has('password') ? 'account__input--error' : '' }}"
                                           required>
                                    @if ($errors->updatePassword->has('password'))
                                        <span class="account__field-error">{{ $errors->updatePassword->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="account__field">
                                    <label for="password_confirmation" class="account__label">Подтверждение пароля</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="account__input" required>
                                </div>
                            </div>
                            <button type="submit" class="account__btn">Сменить пароль</button>
                        </form>
                    </div>

                    {{-- Delete account --}}
                    <div class="account__card account__card--danger">
                        <h3 class="account__card-title">Удалить аккаунт</h3>
                        <p class="account__card-desc">После удаления аккаунта все данные будут безвозвратно утеряны.</p>
                        <form method="POST" action="{{ route('account.destroy') }}" id="delete-account-form">
                            @csrf
                            @method('DELETE')
                            <div class="account__field" style="max-width: 320px;">
                                <label for="delete_password" class="account__label">Введите пароль для подтверждения</label>
                                <input type="password" id="delete_password" name="password"
                                       class="account__input {{ $errors->userDeletion->has('password') ? 'account__input--error' : '' }}"
                                       required>
                                @if ($errors->userDeletion->has('password'))
                                    <span class="account__field-error">{{ $errors->userDeletion->first('password') }}</span>
                                @endif
                            </div>
                            <button type="submit" class="account__btn account__btn--danger"
                                    onclick="return confirm('Вы уверены, что хотите удалить аккаунт?')">
                                Удалить аккаунт
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
