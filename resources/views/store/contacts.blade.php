@extends('layouts.store', ['title' => 'Контакты', 'mainClass' => 'contacts'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Контакты']]])

    <div class="container">
        <h2 class="contacts__title">Контакты</h2>
        <section class="contacts-info">
            <div class="contacts-info__block">
                <div class="contacts-info__list">
                    <div class="contacts-info__list-item">
                        <span>Телефон</span>
                        <a href="tel:+74998880701" class="contact">
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_phone1)">
                                        <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_phone1">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="contact-text">+7 (499) 888-07-01</p>
                        </a>
                        <i>Резервные</i>
                        <a href="tel:+79167858541">+7 (916) 785-85-41</a>
                        <a href="tel:+79151091923">+7 (915) 109-19-23</a>
                    </div>
                    <div class="contacts-info__list-item">
                        <span>Email</span>
                        <a href="mailto:info@colchuga.ru" class="contact">
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_email1)">
                                        <path d="M15 3H3C2.17125 3 1.5075 3.67125 1.5075 4.5L1.5 13.5C1.5 14.3288 2.17125 15 3 15H15C15.8288 15 16.5 14.3288 16.5 13.5V4.5C16.5 3.67125 15.8288 3 15 3ZM15 6L9 9.75L3 6V4.5L9 8.25L15 4.5V6Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_email1">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="contact-text">info@colchuga.ru</p>
                        </a>
                    </div>
                    <div class="contacts-info__list-item">
                        <span>Социальные сети</span>
                        <a href="https://t.me/shieldandswordrus" class="contact" target="_blank">
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_tg_channel)">
                                        <path d="M15.68 3.48c.33-.12.68-.07.93.13.25.2.38.51.35.82 0 .05-.7 7.23-.99 9.6-.2 1.65-.58 1.99-.96 2.04-.8.07-1.41-.53-2.18-1.04l-3.06-2.36c-.59-.45-.2-1.04.13-1.38L12.8 7.4c.48-.52.16-.81-.3-.52l-6.37 4.02c-.93.59-1.35.63-2.32.4l-3.74-1.16c-.81-.25-.83-.81.17-1.2L15.68 3.48z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_tg_channel">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="contact-text">Telegram-канал</p>
                        </a>
                        <a href="https://vk.com/sheildandsword" class="contact" target="_blank">
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_vk)">
                                        <path d="M15.84 5.46c.12-.4 0-.7-.57-.7h-1.89c-.48 0-.7.25-.82.53 0 0-.96 2.33-2.32 3.84-.44.44-.64.58-.88.58-.12 0-.29-.14-.29-.54V5.46c0-.48-.14-.7-.54-.7H6.43c-.3 0-.48.22-.48.43 0 .45.68.55.75 1.81v2.73c0 .6-.11.71-.35.71-.64 0-2.2-2.35-3.12-5.04-.18-.5-.36-.7-.84-.7H.5c-.54 0-.65.25-.65.53 0 .5.64 2.98 2.98 6.26 1.56 2.25 3.76 3.47 5.76 3.47 1.2 0 1.35-.27 1.35-.73v-1.69c0-.54.11-.65.5-.65.28 0 .76.14 1.88 1.22 1.28 1.28 1.49 1.86 2.21 1.86h1.89c.54 0 .81-.27.65-.8-.17-.53-.79-1.3-1.61-2.21-.44-.52-1.1-1.08-1.3-1.36-.28-.36-.2-.52 0-.84 0 0 2.3-3.25 2.54-4.35z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_vk">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="contact-text">Группа ВК</p>
                        </a>
                        <a href="https://youtube.com/@colchuga?si=kxGQBJ1Fy_etH3w" class="contact" target="_blank">
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_youtube)">
                                        <path d="M15.32 4.06c-.28-.02-.53-.03-.78-.03H3.46c-.25 0-.5.01-.78.03C1.03 4.29.75 5.33.75 7.5v3c0 2.17.28 3.21 1.93 3.44.28.02.53.03.78.03h11.08c.25 0 .5-.01.78-.03 1.65-.23 1.93-1.27 1.93-3.44v-3c0-2.17-.28-3.21-1.93-3.44zM7.5 11.25V6.75L12 9l-4.5 2.25z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_youtube">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <p class="contact-text">Youtube-канал</p>
                        </a>
                    </div>

                    <div class="contacts-info__list-item">
                        <span>Адрес</span>
                        <p>Москва, Остаповский проезд, дом 5, строение 10.</p>
                    </div>
                    <div class="contacts-info__list-item">
                        <span>Время работы</span>
                        <p>Пн-Сб 9:00-19:00</p>
                    </div>
                </div>
                <div class="contacts-info__social contacts-info__chat">
                    <span>Чат с поддержкой</span>
                    <p class="chat-description">Напишите нам — мы на связи в рабочее время</p>
                    <button type="button" class="chat-support-btn" id="openChatWidget">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Начать чат
                    </button>
                    {{-- TODO: подключить виджет чата (Jivo/Tawk.to) и привязать кнопку --}}
                </div>
                <div class="map">
                    <iframe src="https://yandex.ru/map-widget/v1/?lang=ru_RU&scroll=true&source=constructor-api&um=constructor%3A6b9da9c4e9eabb7e74c1fe69e16fa39f620e014e31285e1b4efb7f031269c127" frameborder="0" allowfullscreen="true" width="100%" height="400px" style="display: block;"></iframe>
                </div>
            </div>
            <div class="contacts-info__block">
                <div class="form">
                    <h2 class="form__title">Ваши контакты</h2>
                    <p>
                        Как только мы получим вашу контактную информацию, мы свяжемся с вами в
                        рабочее время.
                    </p>

                    @if (session('contact_success'))
                        <div class="alert alert-success" style="margin-bottom:16px;">
                            {{ session('contact_success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <p>
                            <input class="input" type="text" name="name" placeholder="Полное имя *" value="{{ old('name') }}" required>
                            @error('name') <span class="field-error">{{ $message }}</span> @enderror
                            <br>
                            <input class="input" type="email" name="email" placeholder="Email *" value="{{ old('email') }}" required>
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
                            <br>
                            <input class="input" type="tel" name="phone" placeholder="Телефон *" value="{{ old('phone') }}" required>
                            @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                            <br>
                            <textarea name="message" placeholder="Сообщение *" required>{{ old('message') }}</textarea>
                            @error('message') <span class="field-error">{{ $message }}</span> @enderror
                        </p>
                        <div class="form__files">
                            <p>
                                <span class="form__files-text">Прикрепите файлы</span><br>
                                <input class="form__files-input" type="file" name="attachment" accept="audio/*,video/*,image/*">
                            </p>
                        </div>
                        @include('store.partials.captcha-field', ['id' => 'contact'])
                        <p>
                            <label>
                                <input type="checkbox" name="acceptance" value="1" required>
                                <span>Я согласен с обработкой моих данных</span>
                            </label>
                            <br>
                            <button class="form__btn" type="submit">
                                <span class="form__btn-text">Отправить запрос</span>
                                <img src="{{ asset('wp-theme/images/icons/arrow.svg') }}" alt="icon">
                            </button>
                        </p>
                    </form>
                </div>
            </div>
        </section>

        <h2 class="contacts__title">Фирменные магазины</h2>
        <section class="stores-section">
            <div class="store-card" data-animate="fade-up">
                <div class="store-card__gallery">
                    <div class="swiper store-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-12-52.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-12-52.jpg" alt="Щит и Меч — демонстрационный зал">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-04.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-04.jpg" alt="Щит и Меч — интерьер">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-07.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-07.jpg" alt="Щит и Меч — экипировка">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-11.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-11.jpg" alt="Щит и Меч — ассортимент">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-14-1.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-14-1.jpg" alt="Щит и Меч — витрина">
                                </a>
                            </div>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="store-card__info">
                    <h3 class="store-card__name">Щит и Меч</h3>
                    <p class="store-card__type">Демонстрационный зал и пункт выдачи заказов</p>
                    <div class="store-card__detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <p>г. Москва, Остаповский проезд, дом 6, строение 2</p>
                    </div>
                    <p class="store-card__desc">Основной демонстрационный зал компании Щит и Меч. Здесь можно получить консультацию, изучить весь модельный ряд и ассортимент компании, приобрести интересующую экипировку или забрать заказ, сделанный на сайте.</p>
                    <div class="store-card__phones">
                        <a href="tel:+79167858541">+7 (916) 785-85-41</a>
                        <a href="tel:+79151091923">+7 (915) 109-19-23</a>
                    </div>
                    <div class="store-card__contacts">
                        <a href="tel:+79167858541" class="store-card__phone-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            Позвонить
                        </a>
                        <a href="https://wa.me/79167858541" class="store-card__messenger" target="_blank">WhatsApp</a>
                        <a href="https://t.me/colchuga_ru" class="store-card__messenger" target="_blank">Telegram</a>
                    </div>
                </div>
                <div class="store-card__map">
                    <iframe src="https://yandex.eu/map-widget/v1/?um=constructor%3A85161960471&ll=37.704619%2C55.721807&z=17&l=map&pt=37.704619,55.721807,pm2rdm&source=constructor" width="100%" height="300" frameborder="0" allowfullscreen="true"></iframe>
                </div>
            </div>
        </section>

        <h2 class="contacts__title">Торговые представители</h2>
        <div class="reps-tabs">
            <button class="reps-tabs__btn active" data-city="rostov">Ростов-на-Дону</button>
        </div>
        <section class="stores-section">
            <div class="reps-city active" data-city-content="rostov">

                {{-- Ростов-на-Дону | Colchuga Tactical (1) — Чалтырь --}}
                <div class="store-card" data-animate="fade-up">
                    <div class="store-card__gallery">
                        <div class="swiper store-swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <a href="https://colchuga.ru/wp-content/uploads/2024/12/colchuga-tactical-.png" data-fancybox="gallery-rostov1">
                                        <img src="https://colchuga.ru/wp-content/uploads/2024/12/colchuga-tactical-.png" alt="Colchuga Tactical — Чалтырь">
                                    </a>
                                </div>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <div class="store-card__info">
                        <h3 class="store-card__name">Colchuga Tactical</h3>
                        <p class="store-card__type">Торговый представитель</p>
                        <div class="store-card__detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <p>г. Ростов-на-Дону, с. Чалтырь, ул. Красноармейская, д. 82Б</p>
                        </div>
                        <p class="store-card__desc">Магазин тактической экипировки и снаряжения. В ассортименте представлены бронежилеты, комплекты снаряжения, тактические пояса и другая продукция компании «Щит и Меч».</p>
                        <div class="store-card__phones">
                            <a href="tel:+79613199399">+7 961 319-93-99</a>
                        </div>
                        <div class="store-card__contacts">
                            <a href="tel:+79613199399" class="store-card__phone-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                Позвонить
                            </a>
                            <a href="https://wa.me/79613199399?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5!%20%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%B5%D1%81%D1%83%D0%B5%D1%82%20%D1%8D%D0%BA%D0%B8%D0%BF%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0%20%C2%AB%D0%A9%D0%B8%D1%82%20%D0%B8%20%D0%9C%D0%B5%D1%87%C2%BB.%20" class="store-card__messenger" target="_blank">WhatsApp</a>
                            <a href="https://t.me/ColchugaRostov" class="store-card__messenger" target="_blank">Telegram</a>
                        </div>
                    </div>
                    <div class="store-card__map">
                        <iframe src="https://yandex.ru/map-widget/v1/?lang=ru_RU&scroll=true&source=constructor-api&um=constructor%3Ae51e3fa53aa0cc8174febbb5637763756eb5434d18ebad959c664f2a4b72814d" frameborder="0" allowfullscreen="true" width="100%" height="300" style="display: block;"></iframe>
                    </div>
                </div>

                {{-- Ростов-на-Дону | Colchuga Tactical (2) — Ерёменко --}}
                <div class="store-card" data-animate="fade-up" style="margin-top: 40px;">
                    <div class="store-card__gallery">
                        <div class="swiper store-swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.34.jpg" data-fancybox="gallery-rostov2">
                                        <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.34.jpg" alt="Colchuga Tactical — Ерёменко">
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.42.jpg" data-fancybox="gallery-rostov2">
                                        <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.42.jpg" alt="Colchuga Tactical — интерьер">
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.46.jpg" data-fancybox="gallery-rostov2">
                                        <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.46.jpg" alt="Colchuga Tactical — ассортимент">
                                    </a>
                                </div>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <div class="store-card__info">
                        <h3 class="store-card__name">Colchuga Tactical</h3>
                        <p class="store-card__type">Торговый представитель</p>
                        <div class="store-card__detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <p>г. Ростов-на-Дону, ул. Ерёменко, 71/2</p>
                        </div>
                        <p class="store-card__desc">Магазин тактической экипировки и снаряжения. В ассортименте представлены бронежилеты, комплекты снаряжения, тактические пояса и другая продукция компании «Щит и Меч».</p>
                        <div class="store-card__phones">
                            <a href="tel:+79613199399">+7 961 319-93-99</a>
                        </div>
                        <div class="store-card__contacts">
                            <a href="tel:+79613199399" class="store-card__phone-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                Позвонить
                            </a>
                            <a href="https://wa.me/79613199399?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5!%20%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%B5%D1%81%D1%83%D0%B5%D1%82%20%D1%8D%D0%BA%D0%B8%D0%BF%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0%20%C2%AB%D0%A9%D0%B8%D1%82%20%D0%B8%20%D0%9C%D0%B5%D1%87%C2%BB.%20" class="store-card__messenger" target="_blank">WhatsApp</a>
                            <a href="https://t.me/ColchugaRostov" class="store-card__messenger" target="_blank">Telegram</a>
                        </div>
                    </div>
                    <div class="store-card__map">
                        <iframe src="https://yandex.ru/map-widget/v1/?lang=ru_RU&scroll=true&source=constructor-api&um=constructor%3A8362026ea50f1f6e1a4bd841af51607344a486586966a7840c4e9e1f1d73f7ce" frameborder="0" allowfullscreen="true" width="100%" height="300" style="display: block;"></iframe>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
