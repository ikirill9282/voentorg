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
    <div class="wrapper">
        {{-- Mobile burger menu (before header, as in WP theme) --}}
        <nav class="header__bottom-burger__menu">
            <a href="javascript:void(0);" class="close">
                <svg class="close_one" xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve">
                    <g>
                        <path d="M4.59 59.41a2 2 0 0 0 2.83 0L32 34.83l24.59 24.58a2 2 0 0 0 2.83-2.83L34.83 32 59.41 7.41a2 2 0 0 0-2.83-2.83L32 29.17 7.41 4.59a2 2 0 0 0-2.82 2.82L29.17 32 4.59 56.59a2 2 0 0 0 0 2.82z" fill="#000000"></path>
                    </g>
                </svg>
            </a>
            <ul class="menu">
                <li><a href="{{ route('shop.index') }}" class="header__bottom-burger__menu-item">Каталог</a></li>
                <li><a href="{{ route('store.home') }}#about" class="header__bottom-burger__menu-item">О нас</a></li>
                <li><a href="{{ route('blog.index') }}" class="header__bottom-burger__menu-item">Наш блог</a></li>
                <li><a href="{{ route('page.contacts') }}" class="header__bottom-burger__menu-item">Контакты</a></li>
            </ul>
            <div class="social">
                <a href="tel:+74998880701" class="header__top-item">
                    <span class="header__top-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_burger_phone)">
                                <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_burger_phone">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="header__top-text">+7 (499) 888-07-01</span>
                </a>
                <a href="mailto:info@colchuga.ru" class="header__top-item">
                    <span class="header__top-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_burger_email)">
                                <path d="M15 3H3C2.17125 3 1.5075 3.67125 1.5075 4.5L1.5 13.5C1.5 14.3288 2.17125 15 3 15H15C15.8288 15 16.5 14.3288 16.5 13.5V4.5C16.5 3.67125 15.8288 3 15 3ZM15 6L9 9.75L3 6V4.5L9 8.25L15 4.5V6Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_burger_email">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="header__top-text">info@colchuga.ru</span>
                </a>
                <a href="https://wa.me/+79167858541" class="header__top-item">
                    <span class="header__top-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <g clip-path="url(#clip0_burger_wa)">
                                <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_burger_wa">
                                    <rect width="14" height="14" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="header__top-text">WhatsApp</span>
                </a>
                <a href="https://t.me/shieldandswordrus" class="header__top-item">
                    <span class="header__top-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                            <g clip-path="url(#clip0_burger_tg1)">
                                <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_burger_tg1">
                                    <rect width="16" height="16" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="header__top-text">Чат с поддержкой</span>
                </a>
                <a href="https://t.me/shieldandswordrus" class="header__top-item">
                    <span class="header__top-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                            <g clip-path="url(#clip0_burger_tg2)">
                                <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_burger_tg2">
                                    <rect width="16" height="16" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="header__top-text">Telegram канал</span>
                </a>
            </div>
            <div>
                <a href="{{ route('page.policy') }}">Политика конфиденциальности</a>
                <p>Платежные системы</p>
                <div class="pmethods">
                    <img src="{{ asset('wp-theme/images/mir.svg') }}" alt="Мир">
                    <img src="{{ asset('wp-theme/images/visa.svg') }}" alt="Visa">
                    <img src="{{ asset('wp-theme/images/master.svg') }}" alt="Mastercard">
                </div>
            </div>
        </nav>

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
