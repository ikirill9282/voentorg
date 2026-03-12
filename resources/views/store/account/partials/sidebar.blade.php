<aside class="account-sidebar">
    <div class="account-sidebar__user">
        <div class="account-sidebar__avatar">
            {{ mb_strtoupper(mb_substr(Auth::user()->first_name ?: Auth::user()->name, 0, 1)) }}
        </div>
        <div class="account-sidebar__info">
            <span class="account-sidebar__name">{{ Auth::user()->full_name ?: Auth::user()->name }}</span>
            <span class="account-sidebar__email">{{ Auth::user()->phone ?: Auth::user()->email }}</span>
        </div>
    </div>
    <nav class="account-sidebar__nav">
        <a href="{{ route('account.dashboard') }}"
           class="account-sidebar__link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
            </svg>
            Обзор
        </a>
        <a href="{{ route('account.orders') }}"
           class="account-sidebar__link {{ request()->routeIs('account.orders*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>
            </svg>
            Мои заказы
        </a>
        <a href="{{ route('account.addresses') }}"
           class="account-sidebar__link {{ request()->routeIs('account.addresses*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" fill="currentColor"/>
            </svg>
            Адреса доставки
        </a>
        <a href="{{ route('account.bonuses') }}"
           class="account-sidebar__link {{ request()->routeIs('account.bonuses') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M20 6h-2.18c.11-.31.18-.65.18-1a3 3 0 00-3-3c-1.05 0-1.95.56-2.47 1.37L12 4.13l-.53-.76A2.99 2.99 0 009 2a3 3 0 00-3 3c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 12 7.4 15.38 12 17 10.83 14.92 8H20v6z" fill="currentColor"/>
            </svg>
            Бонусы
            @if (Auth::user()->bonus_balance > 0)
                <span class="account-sidebar__badge">{{ number_format(Auth::user()->bonus_balance, 0, '', ' ') }}</span>
            @endif
        </a>
        <a href="{{ route('account.settings') }}"
           class="account-sidebar__link {{ request()->routeIs('account.settings') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 00.12-.61l-1.92-3.32a.488.488 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 00-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6A3.6 3.6 0 1112 8.4a3.6 3.6 0 010 7.2z" fill="currentColor"/>
            </svg>
            Настройки
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="account-sidebar__link account-sidebar__link--logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" fill="currentColor"/>
                </svg>
                Выйти
            </button>
        </form>
    </nav>
</aside>
