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
                <div class="contacts-info__social">
                    <span>Мессенджеры</span>
                    <a href="https://wa.me/+79856959389" class="contact">
                        <span class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <g clip-path="url(#clip0_wa1)">
                                    <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_wa1">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                        <p class="contact-text">Отправить сообщение через WhatsApp</p>
                    </a>
                    <a href="https://t.me/colchuga_ru" class="contact">
                        <span class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                                <g clip-path="url(#clip0_tg1)">
                                    <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_tg1">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                        <p class="contact-text">Сообщения в телеграм</p>
                    </a>
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
        <section class="listMagazin">
            <div class="magazin">
                <div class="magazin-container">
                    <h3>Щит и Меч</h3>
                    <h4>Демонстрационный зал и пункт выдачи заказов</h4>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-12-52.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-12-52.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-04.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-04.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-07.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-07.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-11.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-11.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-14-1.jpg" data-fancybox="gallery-main">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/photo_2025-07-09_13-13-14-1.jpg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Адрес</h5>
                    <p>г. Москва Остаповский проезд, дом 6, строение 2</p>
                    <p>Основной демонстрационный зал компании Щит и Меч. Здесь можно получить консультацию, изучить весь модельный ряд и ассортимент компании, приобрести интересующую экипировку или забрать заказ, сделанный на сайте.</p>
                    <h5 class="title-magazineSection">Телефоны</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_phone2)">
                                        <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_phone2">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="tel:+79167858541">+7 (916) 785-85-41</a>
                        </div>
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_phone3)">
                                        <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_phone3">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="tel:+79151091923">+7 (915) 109-19-23</a>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Мессенджеры</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <g clip-path="url(#clip0_wa2)">
                                        <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_wa2">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://wa.me/79167858541" target="_blank">Сообщение в WhatsApp</a>
                        </div>
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                                    <g clip-path="url(#clip0_tg2)">
                                        <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_tg2">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://t.me/colchuga_ru" target="_blank">Сообщение в Telegram</a>
                        </div>
                    </div>
                </div>
                <div class="mapMagazin">
                    <div class="mapMagazin">
                        <iframe src="https://yandex.eu/map-widget/v1/?um=constructor%3A85161960471&ll=37.704619%2C55.721807&z=17&l=map&pt=37.704619,55.721807,pm2rdm&source=constructor" width="100%" height="400" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <h2 class="contacts__title">Торговые представители</h2>
        <section class="listMagazin">
            {{-- Ростов-на-Дону | Colchuga Tactical (1) --}}
            <div class="magazin">
                <div class="magazin-container">
                    <h3>Ростов-на-Дону | Colchuga Tactical</h3>
                    <h4>Торговый представитель</h4>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/colchuga-tactical-.png" data-fancybox="gallery-rostov1">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/colchuga-tactical-.png" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Адрес</h5>
                    <p>г. Ростов-на-Дону, с. Чалтырь, ул. Красноармейская, д. 82Б</p>
                    <p>Магазин тактической экипировки и снаряжения. В ассортименте представлены бронежилеты, комплекты снаряжения, тактические пояса и другая продукция компании "Щит и Меч.</p>
                    <h5 class="title-magazineSection">Телефон</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_phone4)">
                                        <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_phone4">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="tel:+79613199399">+7 961 319-93-99</a>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Мессенджеры</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <g clip-path="url(#clip0_wa3)">
                                        <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_wa3">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://wa.me/79613199399?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5!%20%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%B5%D1%81%D1%83%D0%B5%D1%82%20%D1%8D%D0%BA%D0%B8%D0%BF%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0%20%C2%AB%D0%A9%D0%B8%D1%82%20%D0%B8%20%D0%9C%D0%B5%D1%87%C2%BB.%20" target="_blank">Сообщение в WhatsApp</a>
                        </div>
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                                    <g clip-path="url(#clip0_tg3)">
                                        <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_tg3">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://t.me/ColchugaRostov" target="_blank">Сообщение в Telegram</a>
                        </div>
                    </div>
                </div>
                <div class="mapMagazin">
                    <iframe src="https://yandex.ru/map-widget/v1/?lang=ru_RU&scroll=true&source=constructor-api&um=constructor%3Ae51e3fa53aa0cc8174febbb5637763756eb5434d18ebad959c664f2a4b72814d" frameborder="0" allowfullscreen="true" width="100%" height="400px" style="display: block;"></iframe>
                </div>
            </div>

            {{-- Ростов-на-Дону | Colchuga Tactical (2) --}}
            <div class="magazin">
                <div class="magazin-container">
                    <h3>Ростов-на-Дону | Colchuga Tactical</h3>
                    <h4>Торговый представитель</h4>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.34.jpg" data-fancybox="gallery-rostov2">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.34.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.42.jpg" data-fancybox="gallery-rostov2">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.42.jpg" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide">
                                <a href="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.46.jpg" data-fancybox="gallery-rostov2">
                                    <img src="https://colchuga.ru/wp-content/uploads/2024/12/2025-08-21-09.28.46.jpg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Адрес</h5>
                    <p>г. Ростов-на-Дону, ул. Ерёменко, 71/2</p>
                    <p>Магазин тактической экипировки и снаряжения. В ассортименте представлены бронежилеты, комплекты снаряжения, тактические пояса и другая продукция компании "Щит и Меч.</p>
                    <h5 class="title-magazineSection">Телефон</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                                    <g clip-path="url(#clip0_phone5)">
                                        <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_phone5">
                                            <rect width="18" height="18" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="tel:+79613199399">+7 961 319-93-99</a>
                        </div>
                    </div>
                    <h5 class="title-magazineSection">Мессенджеры</h5>
                    <div class="contact_magazine">
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <g clip-path="url(#clip0_wa4)">
                                        <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_wa4">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://wa.me/79613199399?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5!%20%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%B5%D1%81%D1%83%D0%B5%D1%82%20%D1%8D%D0%BA%D0%B8%D0%BF%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0%20%C2%AB%D0%A9%D0%B8%D1%82%20%D0%B8%20%D0%9C%D0%B5%D1%87%C2%BB.%20" target="_blank">Сообщение в WhatsApp</a>
                        </div>
                        <div>
                            <span class="contact-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                                    <g clip-path="url(#clip0_tg4)">
                                        <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_tg4">
                                            <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <a href="https://t.me/ColchugaRostov" target="_blank">Сообщение в Telegram</a>
                        </div>
                    </div>
                </div>
                <div class="mapMagazin">
                    <iframe src="https://yandex.ru/map-widget/v1/?lang=ru_RU&scroll=true&source=constructor-api&um=constructor%3A8362026ea50f1f6e1a4bd841af51607344a486586966a7840c4e9e1f1d73f7ce" frameborder="0" allowfullscreen="true" width="100%" height="400px" style="display: block;"></iframe>
                </div>
            </div>
        </section>
    </div>
@endsection
