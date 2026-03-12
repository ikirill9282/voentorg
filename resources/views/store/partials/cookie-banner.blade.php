<div class="cookie-banner" id="cookieBanner" style="display:none;">
    <div class="cookie-banner__content">
        <p>Мы используем файлы cookie для улучшения работы сайта.
           Продолжая использовать сайт, вы соглашаетесь с
           <a href="/page/privacy-policy">политикой конфиденциальности</a>.</p>
        <div class="cookie-banner__buttons">
            <button class="cookie-banner__btn" id="cookieAccept">Принять все</button>
            <button class="cookie-banner__btn cookie-banner__btn--outline" id="cookieReject">Отклонить</button>
            <button class="cookie-banner__btn cookie-banner__btn--outline" id="cookieManage">Настроить</button>
        </div>
    </div>
</div>

<div class="cookie-modal" id="cookieModal" style="display:none;">
    <div class="cookie-modal__overlay"></div>
    <div class="cookie-modal__dialog">
        <div class="cookie-modal__header">
            <h3>Настройки cookie</h3>
            <button class="cookie-modal__close" id="cookieModalClose">&times;</button>
        </div>
        <div class="cookie-modal__body">
            <div class="cookie-modal__item">
                <div class="cookie-modal__item-info">
                    <h4>Необходимые</h4>
                    <p>Обеспечивают базовую функциональность сайта: авторизация, корзина, сессия. Без них сайт не работает.</p>
                </div>
                <label class="cookie-toggle">
                    <input type="checkbox" checked disabled>
                    <span class="cookie-toggle__slider"></span>
                </label>
            </div>
            <div class="cookie-modal__item">
                <div class="cookie-modal__item-info">
                    <h4>Аналитические</h4>
                    <p>Помогают понимать, как пользователи взаимодействуют с сайтом.</p>
                </div>
                <label class="cookie-toggle">
                    <input type="checkbox" id="cookieAnalytics" checked>
                    <span class="cookie-toggle__slider"></span>
                </label>
            </div>
            <div class="cookie-modal__item">
                <div class="cookie-modal__item-info">
                    <h4>Функциональные</h4>
                    <p>Запоминают ваши предпочтения для улучшения пользовательского опыта.</p>
                </div>
                <label class="cookie-toggle">
                    <input type="checkbox" id="cookieFunctional" checked>
                    <span class="cookie-toggle__slider"></span>
                </label>
            </div>
        </div>
        <div class="cookie-modal__footer">
            <button class="cookie-banner__btn" id="cookieSavePrefs">Сохранить настройки</button>
        </div>
    </div>
</div>
