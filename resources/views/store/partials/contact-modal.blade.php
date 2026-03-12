<div class="modal">
    <div class="modal__container">
        <div class="modal__body">
            <div class="form">
                <h2 class="form__title">Ваши контакты</h2>
                <p>Как только мы получим вашу контактную информацию, мы свяжемся с вами в рабочее время.</p>
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <p>
                        <input class="input" type="text" name="name" placeholder="Полное имя *" required><br>
                        <input class="input" type="email" name="email" placeholder="Email *" required><br>
                        <input class="input" type="tel" name="phone" placeholder="Телефон *" required><br>
                        <textarea name="message" placeholder="Сообщение *" required></textarea>
                    </p>
                    @include('store.partials.captcha-field', ['id' => 'contact-modal'])
                    <p>
                        <button class="form__btn" type="submit">
                            <span class="form__btn-text">Отправить запрос</span>
                            <img src="{{ asset('wp-theme/images/icons/arrow.svg') }}" alt="icon">
                        </button>
                    </p>
                </form>
            </div>
            <span class="modal__close">&#10006;</span>
        </div>
    </div>
</div>
