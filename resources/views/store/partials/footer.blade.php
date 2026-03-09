<footer class="footer">
    <div class="container">
        <div class="footer__inner">
            <div class="footer__col">
                <h5>Содержание</h5>
                @foreach ($storeCategories ?? [] as $cat)
                    <a href="{{ route('shop.category', $cat->slug) }}" class="footer__col-item">
                        <span class="footer__col-text">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
            <div class="footer__col">
                <h5>Компания</h5>
                <a href="{{ route('store.home') }}#about" class="footer__col-item">
                    <span class="footer__col-text">О нас</span>
                </a>
                <a href="{{ route('blog.index') }}" class="footer__col-item">
                    <span class="footer__col-text">Наш блог</span>
                </a>
                <a href="{{ route('page.contacts') }}" class="footer__col-item">
                    <span class="footer__col-text">Контакты</span>
                </a>
                <a href="{{ route('page.privacy-policy') }}" class="footer__col-item">
                    <span class="footer__col-text">Политика конфиденциальности</span>
                </a>
                <a href="{{ route('page.kak-sdelat-zakaz') }}" class="footer__col-item">
                    <span class="footer__col-text">Как сделать заказ</span>
                </a>
                <a href="{{ route('page.pravila-torgovli') }}" class="footer__col-item">
                    <span class="footer__col-text">Правила торговли</span>
                </a>
                <a href="{{ route('page.sposob-dostavki') }}" class="footer__col-item">
                    <span class="footer__col-text">Способы доставки</span>
                </a>
                <a href="{{ route('page.sposoby-oplaty') }}" class="footer__col-item">
                    <span class="footer__col-text">Способы оплаты</span>
                </a>
            </div>
            <div class="footer__col">
                <h5>Документация</h5>
                <a href="#" class="footer__col-item form-btn" data-modal="contact">
                    <span class="footer__col-text">Запрос</span>
                </a>
                <a href="{{ asset('wp-theme/catalog25.pdf') }}" class="footer__col-item">
                    <span class="footer__col-text">Номенклатура продукции</span>
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 17 17" fill="none">
                            <path d="M2.83353 7.36668V6.86668H2.33353V7.36668H2.83353ZM7.36686 7.36668V6.86668H6.86686V7.36668H7.36686ZM7.36686 11.9H6.86686V12.4H7.36686V11.9ZM15.3002 3.96668H15.8002V3.75957L15.6537 3.61313L15.3002 3.96668ZM11.9002 0.566681L12.2537 0.213128L12.1073 0.0666809H11.9002V0.566681ZM2.83353 7.86668H3.96686V6.86668H2.83353V7.86668ZM3.33353 12.4667V9.63335H2.33353V12.4667H3.33353ZM3.33353 9.63335V7.36668H2.33353V9.63335H3.33353ZM3.96686 9.13335H2.83353V10.1333H3.96686V9.13335ZM4.6002 8.50001C4.6002 8.84979 4.31664 9.13335 3.96686 9.13335V10.1333C4.86893 10.1333 5.6002 9.40208 5.6002 8.50001H4.6002ZM3.96686 7.86668C4.31664 7.86668 4.6002 8.15023 4.6002 8.50001H5.6002C5.6002 7.59795 4.86893 6.86668 3.96686 6.86668V7.86668ZM6.86686 7.36668V11.9H7.86686V7.36668H6.86686ZM7.36686 12.4H8.5002V11.4H7.36686V12.4ZM10.1335 10.7667V8.50001H9.13353V10.7667H10.1335ZM8.5002 6.86668H7.36686V7.86668H8.5002V6.86668ZM10.1335 8.50001C10.1335 7.59795 9.40226 6.86668 8.5002 6.86668V7.86668C8.84998 7.86668 9.13353 8.15023 9.13353 8.50001H10.1335ZM8.5002 12.4C9.40226 12.4 10.1335 11.6687 10.1335 10.7667H9.13353C9.13353 11.1165 8.84998 11.4 8.5002 11.4V12.4ZM11.4002 6.80001V12.4667H12.4002V6.80001H11.4002ZM11.9002 7.86668H14.7335V6.86668H11.9002V7.86668ZM11.9002 10.1333H13.6002V9.13335H11.9002V10.1333ZM2.2002 5.66668V1.70001H1.2002V5.66668H2.2002ZM14.8002 3.96668V5.66668H15.8002V3.96668H14.8002ZM2.83353 1.06668H11.9002V0.0666809H2.83353V1.06668ZM11.5466 0.920234L14.9466 4.32023L15.6537 3.61313L12.2537 0.213128L11.5466 0.920234ZM2.2002 1.70001C2.2002 1.35023 2.48375 1.06668 2.83353 1.06668V0.0666809C1.93146 0.0666809 1.2002 0.797949 1.2002 1.70001H2.2002ZM1.2002 13.6V15.3H2.2002V13.6H1.2002ZM2.83353 16.9333H14.1669V15.9333H2.83353V16.9333ZM15.8002 15.3V13.6H14.8002V15.3H15.8002ZM14.1669 16.9333C15.0689 16.9333 15.8002 16.2021 15.8002 15.3H14.8002C14.8002 15.6498 14.5166 15.9333 14.1669 15.9333V16.9333ZM1.2002 15.3C1.2002 16.2021 1.93146 16.9333 2.83353 16.9333V15.9333C2.48375 15.9333 2.2002 15.6498 2.2002 15.3H1.2002Z" fill="#404141"></path>
                        </svg>
                    </span>
                </a>
                <a href="/" class="footer__col-item">
                    <span class="footer__col-text">Сертификат</span>
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 17 17" fill="none">
                            <path d="M2.83353 7.36668V6.86668H2.33353V7.36668H2.83353ZM7.36686 7.36668V6.86668H6.86686V7.36668H7.36686ZM7.36686 11.9H6.86686V12.4H7.36686V11.9ZM15.3002 3.96668H15.8002V3.75957L15.6537 3.61313L15.3002 3.96668ZM11.9002 0.566681L12.2537 0.213128L12.1073 0.0666809H11.9002V0.566681ZM2.83353 7.86668H3.96686V6.86668H2.83353V7.86668ZM3.33353 12.4667V9.63335H2.33353V12.4667H3.33353ZM3.33353 9.63335V7.36668H2.33353V9.63335H3.33353ZM3.96686 9.13335H2.83353V10.1333H3.96686V9.13335ZM4.6002 8.50001C4.6002 8.84979 4.31664 9.13335 3.96686 9.13335V10.1333C4.86893 10.1333 5.6002 9.40208 5.6002 8.50001H4.6002ZM3.96686 7.86668C4.31664 7.86668 4.6002 8.15023 4.6002 8.50001H5.6002C5.6002 7.59795 4.86893 6.86668 3.96686 6.86668V7.86668ZM6.86686 7.36668V11.9H7.86686V7.36668H6.86686ZM7.36686 12.4H8.5002V11.4H7.36686V12.4ZM10.1335 10.7667V8.50001H9.13353V10.7667H10.1335ZM8.5002 6.86668H7.36686V7.86668H8.5002V6.86668ZM10.1335 8.50001C10.1335 7.59795 9.40226 6.86668 8.5002 6.86668V7.86668C8.84998 7.86668 9.13353 8.15023 9.13353 8.50001H10.1335ZM8.5002 12.4C9.40226 12.4 10.1335 11.6687 10.1335 10.7667H9.13353C9.13353 11.1165 8.84998 11.4 8.5002 11.4V12.4ZM11.4002 6.80001V12.4667H12.4002V6.80001H11.4002ZM11.9002 7.86668H14.7335V6.86668H11.9002V7.86668ZM11.9002 10.1333H13.6002V9.13335H11.9002V10.1333ZM2.2002 5.66668V1.70001H1.2002V5.66668H2.2002ZM14.8002 3.96668V5.66668H15.8002V3.96668H14.8002ZM2.83353 1.06668H11.9002V0.0666809H2.83353V1.06668ZM11.5466 0.920234L14.9466 4.32023L15.6537 3.61313L12.2537 0.213128L11.5466 0.920234ZM2.2002 1.70001C2.2002 1.35023 2.48375 1.06668 2.83353 1.06668V0.0666809C1.93146 0.0666809 1.2002 0.797949 1.2002 1.70001H2.2002ZM1.2002 13.6V15.3H2.2002V13.6H1.2002ZM2.83353 16.9333H14.1669V15.9333H2.83353V16.9333ZM15.8002 15.3V13.6H14.8002V15.3H15.8002ZM14.1669 16.9333C15.0689 16.9333 15.8002 16.2021 15.8002 15.3H14.8002C14.8002 15.6498 14.5166 15.9333 14.1669 15.9333V16.9333ZM1.2002 15.3C1.2002 16.2021 1.93146 16.9333 2.83353 16.9333V15.9333C2.48375 15.9333 2.2002 15.6498 2.2002 15.3H1.2002Z" fill="#404141"></path>
                        </svg>
                    </span>
                </a>
                <br>
                <h5>Способы оплаты</h5>
                <div class="pmethods">
                    <img src="{{ asset('wp-theme/images/mir.svg') }}">
                    <img src="{{ asset('wp-theme/images/visa.svg') }}">
                    <img src="{{ asset('wp-theme/images/master.svg') }}">
                </div>
            </div>
            <div class="footer__col">
                <h5>Контакты</h5>
                <a href="tel:+74998880701" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_phone_main)">
                                <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_phone_main">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">+7 (499) 888-07-01</span>
                </a>
                <span class="footer__col-label" style="color: #a5a1ab; margin-bottom: 5px; font-weight: 400; font-size: 14px; line-height: 20px;">Резервные:</span>
                <a href="tel:+79167858541" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_phone_reserve1)">
                                <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_phone_reserve1">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">+7 (916) 785-85-41</span>
                </a>
                <a href="tel:+79151091923" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_phone_reserve2)">
                                <path d="M7.43435 4.53593C7.21948 2.0643 4.64098 0.851551 4.53185 0.802051C4.42984 0.754097 4.31552 0.738775 4.20448 0.758176C1.22773 1.25205 0.779976 2.98455 0.761976 3.05655C0.737486 3.15689 0.741381 3.26205 0.773226 3.3603C4.32373 14.3763 11.7026 16.4182 14.1281 17.0898C14.3149 17.1416 14.469 17.1832 14.586 17.2214C14.7195 17.265 14.8645 17.2566 14.9921 17.1978C15.0664 17.1641 16.8191 16.3383 17.2477 13.6451C17.2667 13.5275 17.2478 13.4069 17.1937 13.3008C17.1555 13.2266 16.2386 11.4817 13.6961 10.8652C13.6098 10.8431 13.5195 10.8423 13.4329 10.8629C13.3463 10.8835 13.266 10.9248 13.1989 10.9833C12.3967 11.6684 11.2886 12.3986 10.8105 12.4739C7.60535 10.9068 5.81548 7.89968 5.74798 7.3293C5.7086 7.00868 6.44323 5.88255 7.2881 4.9668C7.3412 4.90917 7.38165 4.84105 7.40684 4.76685C7.43203 4.69264 7.4414 4.61398 7.43435 4.53593V4.53593Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_phone_reserve2">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">+7 (915) 109-19-23</span>
                </a>
                <a href="mailto:info@colchuga.ru" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 18" fill="none">
                            <g clip-path="url(#clip0_1_7432)">
                                <path d="M15 3H3C2.17125 3 1.5075 3.67125 1.5075 4.5L1.5 13.5C1.5 14.3288 2.17125 15 3 15H15C15.8288 15 16.5 14.3288 16.5 13.5V4.5C16.5 3.67125 15.8288 3 15 3ZM15 6L9 9.75L3 6V4.5L9 8.25L15 4.5V6Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_1_7432">
                                    <rect width="18" height="18" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">info@colchuga.ru</span>
                </a>
                <a href="https://wa.me/+79167858541" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <g clip-path="url(#clip0_1_7422)">
                                <path d="M7 0C3.14008 0 0 3.14008 0 7C0 8.34808 0.3815 9.65008 1.106 10.7777L0.01925 13.6033C-0.000379306 13.6554 -0.00474601 13.7121 0.00666189 13.7666C0.0180698 13.8211 0.0447794 13.8712 0.0836589 13.911C0.122538 13.9509 0.171976 13.9788 0.226175 13.9916C0.280374 14.0044 0.337088 14.0014 0.389667 13.9831L3.30575 12.9418C4.41355 13.6341 5.69371 14.0007 7 14C10.8599 14 14 10.8599 14 7C14 3.14008 10.8599 0 7 0ZM10.5 9.04167C10.5 9.69792 9.69208 10.5 8.75 10.5C7.83358 10.5 6.07425 9.40392 5.33517 8.66483C4.59608 7.92517 3.5 6.16583 3.5 5.25C3.5 4.30733 4.30208 3.5 4.95833 3.5H5.54167C5.59607 3.50006 5.64937 3.51533 5.69556 3.54409C5.74174 3.57285 5.77895 3.61395 5.803 3.66275C5.80358 3.66333 6.15475 4.37733 6.38458 4.82533C6.64358 5.33108 6.1565 5.92608 5.87942 6.20667C5.97858 6.461 6.21017 6.965 6.62258 7.37742C7.035 7.78983 7.539 8.022 7.79333 8.12058C8.07333 7.84292 8.66833 7.35525 9.17467 7.61542C9.62267 7.84583 10.3361 8.19642 10.3367 8.19642C10.3857 8.22042 10.4269 8.2577 10.4558 8.304C10.4847 8.3503 10.5 8.40377 10.5 8.45833V9.04167V9.04167Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_1_7422">
                                    <rect width="14" height="14" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">WhatsApp</span>
                </a>
                <a href="https://t.me/shieldandswordrus" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                            <g clip-path="url(#clip0_1_7426)">
                                <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_1_7426">
                                    <rect width="16" height="16" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">Telegram канал</span>
                </a>
                <a href="https://t.me/colchuga_ru" class="footer__col-item">
                    <span class="footer__col-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                            <g clip-path="url(#clip0_1_7426)">
                                <path d="M6.27812 10.1206L6.01345 13.8433C6.39212 13.8433 6.55612 13.6806 6.75279 13.4853L8.52812 11.7886L12.2068 14.4826C12.8815 14.8586 13.3568 14.6606 13.5388 13.8619L15.9535 2.54728L15.9541 2.54661C16.1681 1.54928 15.5935 1.15928 14.9361 1.40394L0.742785 6.83794C-0.225881 7.21394 -0.211215 7.75394 0.578119 7.99861L4.20679 9.12728L12.6355 3.85328C13.0321 3.59061 13.3928 3.73595 13.0961 3.99861L6.27812 10.1206Z" fill="#A03611"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_1_7426">
                                    <rect width="16" height="16" fill="white"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="footer__col-text">Чат с  поддержкой</span>
                </a>
                <a href="javascript:void(0)" class="form-btn" data-modal="contact">
                    <span class="footer__col-text">Получить консультацию</span>
                </a>
                <span class="footer__col-text">Пн-Сб 9:00-19:00</span>
            </div>
        </div>
        <div class="footer__accordion">
            <div class="accordion-item">
                <div class="accordion-item__btn">
                    <span class="accordion-item__btn-text">Содержание</span>
                    <div class="accordion-item__btn-icon">
                        <span class="accordion-item__btn-icon-right">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 792.033 792.033" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M617.858 370.896 221.513 9.705c-13.006-12.94-34.099-12.94-47.105 0-13.006 12.939-13.006 33.934 0 46.874l372.447 339.438-372.414 339.437c-13.006 12.94-13.006 33.935 0 46.874s34.099 12.939 47.104 0l396.346-361.191c6.932-6.898 9.904-16.043 9.441-25.087.431-9.078-2.54-18.222-9.474-25.154z" fill="#404141" data-original="#000000"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="accordion-item__btn-icon-up">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 240.835 240.835" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M129.007 57.819c-4.68-4.68-12.499-4.68-17.191 0L3.555 165.803c-4.74 4.74-4.74 12.427 0 17.155 4.74 4.74 12.439 4.74 17.179 0l99.683-99.406 99.671 99.418c4.752 4.74 12.439 4.74 17.191 0 4.74-4.74 4.74-12.427 0-17.155L129.007 57.819z" fill="#404141" data-original="#404141"></path>
                                </g>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="accordion-item__list">
                    @foreach ($storeCategories ?? [] as $cat)
                        <a href="{{ route('shop.category', $cat->slug) }}" class="footer__col-item">
                            <span class="footer__col-text">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-item__btn">
                    <span class="accordion-item__btn-text">Компания</span>
                    <div class="accordion-item__btn-icon">
                        <span class="accordion-item__btn-icon-right">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 792.033 792.033" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M617.858 370.896 221.513 9.705c-13.006-12.94-34.099-12.94-47.105 0-13.006 12.939-13.006 33.934 0 46.874l372.447 339.438-372.414 339.437c-13.006 12.94-13.006 33.935 0 46.874s34.099 12.939 47.104 0l396.346-361.191c6.932-6.898 9.904-16.043 9.441-25.087.431-9.078-2.54-18.222-9.474-25.154z" fill="#404141" data-original="#000000"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="accordion-item__btn-icon-up">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 240.835 240.835" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M129.007 57.819c-4.68-4.68-12.499-4.68-17.191 0L3.555 165.803c-4.74 4.74-4.74 12.427 0 17.155 4.74 4.74 12.439 4.74 17.179 0l99.683-99.406 99.671 99.418c4.752 4.74 12.439 4.74 17.191 0 4.74-4.74 4.74-12.427 0-17.155L129.007 57.819z" fill="#404141" data-original="#404141"></path>
                                </g>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="accordion-item__list">
                    <a href="{{ route('store.home') }}#about">О нас</a>
                    <a href="{{ route('blog.index') }}">Наш блог</a>
                    <a href="{{ route('page.contacts') }}">Контакты</a>
                    <a href="{{ route('page.privacy-policy') }}">Политика конфиденциальности</a>
                    <a href="{{ route('page.kak-sdelat-zakaz') }}">Как сделать заказ</a>
                    <a href="{{ route('page.pravila-torgovli') }}">Правила торговли</a>
                    <a href="{{ route('page.sposob-dostavki') }}">Способы доставки</a>
                    <a href="{{ route('page.sposoby-oplaty') }}">Способы оплаты</a>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-item__btn">
                    <span class="accordion-item__btn-text">Документация</span>
                    <div class="accordion-item__btn-icon">
                        <span class="accordion-item__btn-icon-right">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 792.033 792.033" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M617.858 370.896 221.513 9.705c-13.006-12.94-34.099-12.94-47.105 0-13.006 12.939-13.006 33.934 0 46.874l372.447 339.438-372.414 339.437c-13.006 12.94-13.006 33.935 0 46.874s34.099 12.939 47.104 0l396.346-361.191c6.932-6.898 9.904-16.043 9.441-25.087.431-9.078-2.54-18.222-9.474-25.154z" fill="#404141" data-original="#000000"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="accordion-item__btn-icon-up">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="20" height="20" x="0" y="0" viewBox="0 0 240.835 240.835" style="enable-background: new 0 0 512 512" xml:space="preserve" class="">
                                <g>
                                    <path d="M129.007 57.819c-4.68-4.68-12.499-4.68-17.191 0L3.555 165.803c-4.74 4.74-4.74 12.427 0 17.155 4.74 4.74 12.439 4.74 17.179 0l99.683-99.406 99.671 99.418c4.752 4.74 12.439 4.74 17.191 0 4.74-4.74 4.74-12.427 0-17.155L129.007 57.819z" fill="#404141" data-original="#404141"></path>
                                </g>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="accordion-item__list">
                    <a href="#" class="form-btn" data-modal="contact">Запрос</a>
                    <a href="{{ asset('wp-theme/catalog25.pdf') }}">Номенклатура продукции</a>
                    <a href="#">Сертификат</a>
                </div>
            </div>
        </div>
        <hr />
        <a href="/" class="footer__bottom"><span>&copy; {{ date('Y') }} Кольчуга. Все права защищены.</span></a>
    </div>
</footer>
