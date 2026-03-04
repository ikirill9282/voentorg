<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'COLCHUGA — Снаряжение, которое работает' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'COLCHUGA — производство и продажа тактического снаряжения, бронежилетов, баллистической защиты.' }}">
    <link rel="icon" href="{{ asset('wp-theme/favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/store.css', 'resources/css/store-overrides.css', 'resources/js/store.js'])
</head>
<body>
    {{-- Mobile burger menu panel (70% width, white, accordion) — outside .wrapper so it stays in place --}}
    <nav class="header__bottom-burger__menu">
            <button class="burger-menu__close" aria-label="Закрыть меню">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 64 64">
                    <path d="M4.59 59.41a2 2 0 0 0 2.83 0L32 34.83l24.59 24.58a2 2 0 0 0 2.83-2.83L34.83 32 59.41 7.41a2 2 0 0 0-2.83-2.83L32 29.17 7.41 4.59a2 2 0 0 0-2.82 2.82L29.17 32 4.59 56.59a2 2 0 0 0 0 2.82z" fill="#333"/>
                </svg>
            </button>

            <div class="burger-menu__content">
                <ul class="burger-menu__list">
                    {{-- 1. Каталог с аккордеоном --}}
                    <li class="burger-menu__item burger-menu__item--expandable">
                        <div class="burger-menu__item-row">
                            <a href="{{ route('shop.index') }}" class="burger-menu__link">Каталог</a>
                            <button class="burger-menu__toggle" aria-label="Развернуть каталог">
                                <svg class="burger-menu__arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                        </div>
                        <ul class="burger-menu__submenu">
                            <li class="burger-menu__subitem">
                                <a href="{{ route('shop.index') }}" class="burger-menu__sublink burger-menu__sublink--all">Все товары</a>
                            </li>
                            @foreach ($storeCategories ?? [] as $cat)
                                <li class="burger-menu__subitem {{ $cat->children && $cat->children->count() ? 'burger-menu__subitem--expandable' : '' }}">
                                    <div class="burger-menu__subitem-row">
                                        <a href="{{ route('shop.category', $cat->slug) }}" class="burger-menu__sublink">{{ $cat->name }}</a>
                                        @if ($cat->children && $cat->children->count())
                                            <button class="burger-menu__toggle" aria-label="Развернуть {{ $cat->name }}">
                                                <svg class="burger-menu__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            </button>
                                        @endif
                                    </div>
                                    @if ($cat->children && $cat->children->count())
                                        <ul class="burger-menu__submenu burger-menu__submenu--nested">
                                            @foreach ($cat->children as $child)
                                                <li class="burger-menu__subitem">
                                                    <a href="{{ route('shop.category', $child->slug) }}" class="burger-menu__sublink burger-menu__sublink--child">{{ $child->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    {{-- 2-4. Простые ссылки --}}
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="{{ route('blog.index') }}" class="burger-menu__link">Новости</a>
                        </div>
                    </li>
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="{{ route('page.contacts') }}" class="burger-menu__link">Контакты и магазины</a>
                        </div>
                    </li>
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="{{ Auth::check() ? route('account.dashboard') : route('login') }}" class="burger-menu__link">{{ Auth::check() ? 'Личный кабинет' : 'Войти' }}</a>
                        </div>
                    </li>

                    {{-- Разделитель --}}
                    <li class="burger-menu__separator"></li>

                    {{-- 5. Телефон с резервными номерами --}}
                    <li class="burger-menu__item burger-menu__item--expandable">
                        <div class="burger-menu__item-row">
                            <a href="tel:+74998880701" class="burger-menu__link burger-menu__link--contact">+7(499) 888-07-01</a>
                            <button class="burger-menu__toggle" aria-label="Показать резервные номера">
                                <svg class="burger-menu__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                        </div>
                        <div class="burger-menu__submenu burger-menu__submenu--phones">
                            <p class="burger-menu__reserve-label">Резервные:</p>
                            <a href="tel:+79167858541" class="burger-menu__sublink burger-menu__sublink--phone">+7(916) 785-85-41</a>
                            <a href="tel:+79151091923" class="burger-menu__sublink burger-menu__sublink--phone">+7(915) 109-19-23</a>
                        </div>
                    </li>

                    {{-- 6. Email --}}
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="mailto:info@colchuga.ru" class="burger-menu__link burger-menu__link--contact">info@colchuga.ru</a>
                        </div>
                    </li>

                    {{-- 7. Telegram канал --}}
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="https://t.me/shieldandswordrus" class="burger-menu__link burger-menu__link--social">
                                <svg class="burger-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 16 14" fill="none">
                                    <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="currentColor"/>
                                </svg>
                                Telegram канал
                            </a>
                        </div>
                    </li>

                    {{-- 8. Чат с поддержкой в Telegram --}}
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="https://t.me/colchuga_ru" class="burger-menu__link burger-menu__link--social">
                                <svg class="burger-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 16 14" fill="none">
                                    <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="currentColor"/>
                                </svg>
                                Чат с поддержкой в Telegram
                            </a>
                        </div>
                    </li>

                    {{-- 9. Чат с поддержкой в Max --}}
                    <li class="burger-menu__item">
                        <div class="burger-menu__item-row">
                            <a href="#" class="burger-menu__link burger-menu__link--social">
                                <svg class="burger-menu__icon burger-menu__icon--max" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="18" height="18" viewBox="0 0 1000 1000">
                                    <defs>
                                        <linearGradient id="burger-max-a"><stop offset="0" stop-color="#4cf"/><stop offset=".662" stop-color="#53e"/><stop offset="1" stop-color="#93d"/></linearGradient>
                                        <linearGradient id="burger-max-c" x1="117.847" x2="1000" y1="760.536" y2="500" gradientUnits="userSpaceOnUse" href="#burger-max-a"/>
                                    </defs>
                                    <rect width="1000" height="1000" fill="url(#burger-max-c)" ry="249.681"/>
                                    <path fill="#fff" fill-rule="evenodd" d="M508.211 878.328c-75.007 0-109.864-10.95-170.453-54.75-38.325 49.275-159.686 87.783-164.979 21.9 0-49.456-10.95-91.248-23.36-136.873-14.782-56.21-31.572-118.807-31.572-209.508 0-216.626 177.754-379.597 388.357-379.597 210.785 0 375.947 171.001 375.947 381.604.707 207.346-166.595 376.118-373.94 377.224m3.103-571.585c-102.564-5.292-182.499 65.7-200.201 177.024-14.6 92.162 11.315 204.398 33.397 210.238 10.585 2.555 37.23-18.98 53.837-35.587a189.8 189.8 0 0 0 92.71 33.032c106.273 5.112 197.08-75.794 204.215-181.95 4.154-106.382-77.67-196.486-183.958-202.574Z" clip-rule="evenodd"/>
                                </svg>
                                Чат с поддержкой в Max
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
    </nav>

    <div class="wrapper">
        {{-- Overlay inside wrapper — dims shifted content, closes menu on click --}}
        <div class="burger-overlay"></div>

        @include('store.partials.header')

        <main class="{{ $mainClass ?? '' }}">
            @if (session('success'))
                <div class="container">
                    <div class="alert alert-success">{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="container">
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        @include('store.partials.footer')
        @include('store.partials.contact-modal')
        @include('store.partials.message-modal')
    </div>

    <div id="toast-container"></div>
    @stack('scripts')
</body>
</html>
