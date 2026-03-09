@extends('layouts.store', ['title' => 'Политика конфиденциальности', 'mainClass' => 'privacy-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Политика конфиденциальности']]])

    <section class="privacy">
        <div class="container">
            <h1 class="privacy__title">Политика конфиденциальности</h1>
            <p class="privacy__updated">Дата последнего обновления: 01.03.2026</p>

            <div class="privacy__content">

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">1. Общие положения</h2>
                    <p>Настоящая Политика конфиденциальности (далее — «Политика») определяет порядок обработки и защиты персональных данных физических лиц, использующих сайт интернет-магазина <strong>«Щит и Меч» (COLCHUGA)</strong> (далее — «Оператор», «Мы»).</p>
                    <p>Используя сайт, вы соглашаетесь с условиями данной Политики. Если вы не согласны с условиями, пожалуйста, не используйте сайт.</p>
                    <p>Оператор оставляет за собой право вносить изменения в настоящую Политику. Актуальная редакция всегда доступна на данной странице.</p>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">2. Какие данные мы собираем</h2>
                    <p>Мы можем собирать следующие персональные данные:</p>
                    <ul class="privacy__list">
                        <li><strong>Контактные данные:</strong> фамилия, имя, отчество, номер телефона, адрес электронной почты</li>
                        <li><strong>Данные для доставки:</strong> почтовый адрес, город, индекс</li>
                        <li><strong>Данные заказа:</strong> история покупок, содержимое корзины, предпочтения по товарам</li>
                        <li><strong>Технические данные:</strong> IP-адрес, тип браузера, операционная система, cookies</li>
                        <li><strong>Данные обратной связи:</strong> содержание сообщений через форму обратной связи</li>
                    </ul>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">3. Цели обработки персональных данных</h2>
                    <p>Персональные данные обрабатываются в следующих целях:</p>
                    <ul class="privacy__list">
                        <li>Оформление и доставка заказов</li>
                        <li>Связь с клиентом для уточнения деталей заказа</li>
                        <li>Информирование об акциях, новинках и специальных предложениях (с согласия пользователя)</li>
                        <li>Улучшение качества обслуживания и работы сайта</li>
                        <li>Исполнение обязательств по договору купли-продажи</li>
                        <li>Обработка обращений через форму обратной связи</li>
                    </ul>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">4. Защита персональных данных</h2>
                    <p>Оператор принимает необходимые организационные и технические меры для защиты персональных данных от несанкционированного доступа, уничтожения, изменения, блокирования, копирования, распространения, а также от иных неправомерных действий третьих лиц.</p>
                    <div class="privacy__highlight">
                        <p>Мы используем шифрование SSL/TLS для безопасной передачи данных между вашим браузером и нашим сервером. Все платежные операции проводятся через защищённые платёжные шлюзы.</p>
                    </div>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">5. Передача данных третьим лицам</h2>
                    <p>Оператор не передаёт персональные данные третьим лицам, за исключением следующих случаев:</p>
                    <ul class="privacy__list">
                        <li>С согласия субъекта персональных данных</li>
                        <li>По требованию законодательства Российской Федерации</li>
                        <li>Для доставки заказов — передача данных курьерским службам и службам доставки (СДЭК, Почта России и др.)</li>
                        <li>Для обработки платежей — передача данных платёжным системам</li>
                    </ul>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">6. Сроки хранения данных</h2>
                    <p>Персональные данные хранятся не дольше, чем этого требуют цели обработки, если иное не предусмотрено законодательством Российской Федерации.</p>
                    <p>Данные заказов хранятся в течение 5 лет с момента последнего заказа в целях исполнения гарантийных обязательств и требований бухгалтерского учёта.</p>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">7. Файлы cookie</h2>
                    <p>Сайт использует файлы cookie для обеспечения корректной работы, персонализации контента и анализа трафика. Виды используемых cookie:</p>
                    <div class="privacy__cookies-grid">
                        <div class="privacy__cookie-card">
                            <h4>Необходимые</h4>
                            <p>Обеспечивают базовую функциональность сайта: авторизация, корзина, сессия</p>
                        </div>
                        <div class="privacy__cookie-card">
                            <h4>Аналитические</h4>
                            <p>Помогают нам понимать, как пользователи взаимодействуют с сайтом</p>
                        </div>
                        <div class="privacy__cookie-card">
                            <h4>Функциональные</h4>
                            <p>Запоминают ваши предпочтения для улучшения пользовательского опыта</p>
                        </div>
                    </div>
                    <p>Вы можете отключить cookie в настройках браузера, однако это может повлиять на функциональность сайта.</p>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">8. Права пользователя</h2>
                    <p>Вы имеете право:</p>
                    <ul class="privacy__list">
                        <li>Запросить информацию о хранимых персональных данных</li>
                        <li>Потребовать исправления неточных данных</li>
                        <li>Потребовать удаления персональных данных</li>
                        <li>Отозвать согласие на обработку персональных данных</li>
                        <li>Подать жалобу в уполномоченный орган по защите прав субъектов персональных данных</li>
                    </ul>
                </div>

                <div class="privacy__section" data-animate="fade-up">
                    <div class="privacy__section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A03611" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <h2 class="privacy__heading">9. Контактная информация</h2>
                    <p>По всем вопросам, связанным с обработкой персональных данных, вы можете обратиться к нам:</p>
                    <div class="privacy__contact-card">
                        <div class="privacy__contact-row">
                            <span class="privacy__contact-label">Email:</span>
                            <a href="mailto:info@colchuga.ru" class="privacy__contact-value">info@colchuga.ru</a>
                        </div>
                        <div class="privacy__contact-row">
                            <span class="privacy__contact-label">Телефон:</span>
                            <a href="tel:+74998880701" class="privacy__contact-value">+7 (499) 888-07-01</a>
                        </div>
                        <div class="privacy__contact-row">
                            <span class="privacy__contact-label">Адрес:</span>
                            <span class="privacy__contact-value">Москва, Остаповский проезд, дом 5, строение 10</span>
                        </div>
                    </div>
                </div>

                <div class="privacy__section privacy__section--legal" data-animate="fade-up">
                    <h2 class="privacy__heading">10. Применимое законодательство</h2>
                    <p>Настоящая Политика разработана в соответствии с Федеральным законом от 27.07.2006 N 152-ФЗ «О персональных данных» и иными нормативными правовыми актами Российской Федерации в области защиты и обработки персональных данных.</p>
                </div>

            </div>
        </div>
    </section>
@endsection
