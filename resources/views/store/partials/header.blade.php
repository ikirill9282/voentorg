<header class="header">
    <div class="container">
        <section class="header__main">
            {{-- Mobile burger (left) --}}
            <div class="header__burger">
                <svg width="20" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 1.5H16M0 7.23913H16M0 12.5H8" stroke="#898121" stroke-width="2"></path>
                </svg>
            </div>

            {{-- Logo --}}
            <a href="/" class="header__logo">
                <img width="134" height="36" src="{{ asset('wp-theme/images/logo-1.svg') }}" alt="Кольчуга">
            </a>

            {{-- Desktop navigation --}}
            <nav class="header__nav">
                <div class="header__nav-catalog-wrapper">
                    <a href="{{ route('shop.index') }}" class="header__nav-link header__nav-catalog-trigger">Каталог</a>
                    <div class="header__catalog-dropdown">
                        <div class="header__catalog-dropdown-inner">
                            @foreach ($storeCategories ?? [] as $cat)
                                <div class="header__catalog-col">
                                    <a href="{{ route('shop.category', $cat->slug) }}" class="header__catalog-parent">{{ $cat->name }}</a>
                                    @if ($cat->children && $cat->children->count())
                                        <ul class="header__catalog-children">
                                            @foreach ($cat->children as $child)
                                                <li><a href="{{ route('shop.category', $child->slug) }}">{{ $child->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('blog.index') }}" class="header__nav-link">Новости</a>
                <a href="{{ route('page.contacts') }}" class="header__nav-link">Контакты и магазины</a>
                <a href="{{ Auth::check() ? route('account.dashboard') : route('login') }}" class="header__nav-link">
                    {{ Auth::check() ? 'Личный кабинет' : 'Войти' }}
                </a>
            </nav>

            {{-- Desktop right: socials + contacts + cart + search --}}
            <div class="header__right">
                <div class="header__socials">
                    <a href="https://t.me/shieldandswordrus" class="header__social-icon" title="Telegram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="28" viewBox="0 0 16 14" fill="none">
                            <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#7A7D85"></path>
                        </svg>
                    </a>
                    <a href="#" class="header__social-icon" title="Max">
                        <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="32" height="32" viewBox="0 0 1000 1000">
                            <rect width="1000" height="1000" fill="#7A7D85" ry="249.681"/>
                            <path fill="#fff" fill-rule="evenodd" d="M508.211 878.328c-75.007 0-109.864-10.95-170.453-54.75-38.325 49.275-159.686 87.783-164.979 21.9 0-49.456-10.95-91.248-23.36-136.873-14.782-56.21-31.572-118.807-31.572-209.508 0-216.626 177.754-379.597 388.357-379.597 210.785 0 375.947 171.001 375.947 381.604.707 207.346-166.595 376.118-373.94 377.224m3.103-571.585c-102.564-5.292-182.499 65.7-200.201 177.024-14.6 92.162 11.315 204.398 33.397 210.238 10.585 2.555 37.23-18.98 53.837-35.587a189.8 189.8 0 0 0 92.71 33.032c106.273 5.112 197.08-75.794 204.215-181.95 4.154-106.382-77.67-196.486-183.958-202.574Z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>

                <div class="header__contacts-desktop">
                    <a href="tel:+74998880701" class="header__contact-link">+7 (499) 888-07-01</a>
                    <a href="mailto:info@colchuga.ru" class="header__contact-link">info@colchuga.ru</a>
                </div>

                {{-- Account --}}
                <a class="header__account" href="{{ Auth::check() ? route('account.dashboard') : route('login') }}" title="{{ Auth::check() ? 'Личный кабинет' : 'Войти' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4" stroke="#898121" stroke-width="1.8" fill="none"/>
                        <path d="M4 21c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="#898121" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                    </svg>
                </a>

                {{-- Cart --}}
                <a class="header__cart" href="{{ route('cart.index') }}">
                    <svg width="28" height="26" viewBox="0 0 46 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M45.973 9.973l-2.598 13.506a4.799 4.799 0 01-1.753 2.851 5.282 5.282 0 01-3.273 1.126h-24.7l.937 4.845h22.89c1.011 0 2 .284 2.84.816a4.91 4.91 0 011.882 2.175c.387.885.488 1.86.29 2.8a4.773 4.773 0 01-1.398 2.48 5.203 5.203 0 01-2.617 1.326 5.372 5.372 0 01-2.952-.276 5.063 5.063 0 01-2.294-1.784 4.669 4.669 0 01-.86-2.692c0-.55.1-1.097.297-1.615h-12.52c.197.518.298 1.064.298 1.615a4.642 4.642 0 01-.577 2.245 4.936 4.936 0 01-1.61 1.737 5.28 5.28 0 01-2.273.833c-.818.1-1.65.01-2.423-.262a5.132 5.132 0 01-2.019-1.295 4.773 4.773 0 01-1.156-2.035 4.601 4.601 0 01-.03-2.31 4.757 4.757 0 011.102-2.063L5.558 3.23H1.703a1.75 1.75 0 01-1.204-.473A1.574 1.574 0 010 1.615C0 1.187.18.775.499.473.819.17 1.252 0 1.703 0h3.855a3.522 3.522 0 012.175.75A3.2 3.2 0 018.9 2.645l1.043 5.43H44.29c.25 0 .495.053.72.155.224.1.422.248.58.43.164.177.284.387.35.615.067.227.078.466.033.698z" fill="#898121"/>
                    </svg>
                    <div class="basket-counter" style="{{ ($storeCartSummary['total_quantity'] ?? 0) > 0 ? '' : 'display: none;' }}">
                        <span>{{ $storeCartSummary['total_quantity'] ?? 0 }}</span>
                    </div>
                </a>

                {{-- Search --}}
                <div class="header__search">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.9 17.3L10.3 11.7C9.8 12.1 9.225 12.4167 8.575 12.65C7.925 12.8833 7.23333 13 6.5 13C4.68333 13 3.146 12.371 1.888 11.113C0.629333 9.85433 0 8.31667 0 6.5C0 4.68333 0.629333 3.14567 1.888 1.887C3.146 0.629 4.68333 0 6.5 0C8.31667 0 9.85433 0.629 11.113 1.887C12.371 3.14567 13 4.68333 13 6.5C13 7.23333 12.8833 7.925 12.65 8.575C12.4167 9.225 12.1 9.8 11.7 10.3L17.325 15.925C17.5083 16.1083 17.6 16.3333 17.6 16.6C17.6 16.8667 17.5 17.1 17.3 17.3C17.1167 17.4833 16.8833 17.575 16.6 17.575C16.3167 17.575 16.0833 17.4833 15.9 17.3ZM6.5 11C7.75 11 8.81267 10.5627 9.688 9.688C10.5627 8.81267 11 7.75 11 6.5C11 5.25 10.5627 4.18733 9.688 3.312C8.81267 2.43733 7.75 2 6.5 2C5.25 2 4.18733 2.43733 3.312 3.312C2.43733 4.18733 2 5.25 2 6.5C2 7.75 2.43733 8.81267 3.312 9.688C4.18733 10.5627 5.25 11 6.5 11Z" fill="#898121"></path>
                    </svg>
                </div>
            </div>
        </section>

        {{-- Search modal --}}
        <div class="search-modal">
            <div class="search-modal__container">
                <div class="search-modal__body">
                    <div class="form-search">
                        <form action="{{ route('shop.index') }}" method="GET">
                            <input type="text" placeholder="Поиск товаров..." class="search-input" name="q" value="{{ request('q') }}" autocomplete="off">
                            <button type="submit" aria-label="Поиск" class="search-icon"></button>
                        </form>
                    </div>
                    <span class="search-modal__close">&#10006;</span>
                    <div class="search-result"></div>
                </div>
            </div>
        </div>
    </div>
</header>
