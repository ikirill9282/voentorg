@extends('layouts.store', ['title' => 'Оформление заказа', 'mainClass' => 'cart'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Оформление заказа']]])

    <section class="page__wrapper">
        <div class="container"><h2 class="basket__title">Оформление заказа</h2></div>

        @if ($cart['is_empty'])
            <div class="container">
                <p>Корзина пуста. Добавьте товары перед оформлением.</p>
                <a href="{{ route('shop.index') }}" class="basket__btn checkout-button wc-forward">В каталог</a>
            </div>
        @else
            <form method="POST" action="{{ route('checkout.store') }}" class="checkout woocommerce-checkout" id="checkout-form">
                @csrf
                <div class="container checkout_inner">
                    <div>
                        <h2>Варианты доставки</h2>
                        <ul id="shipping_method" class="woocommerce-shipping-methods">
                            @foreach ($shippingMethods as $method)
                                <li>
                                    <input type="radio" name="shipping_method_id" id="shipping_{{ $method->id }}"
                                           value="{{ $method->id }}" class="shipping_method"
                                           data-code="{{ $method->code }}"
                                           data-price="{{ $method->price }}"
                                           data-target="{{ match($method->code) {
                                               'free_moscow' => '.shipping',
                                               'free_russia' => '.for-russia',
                                               'free_regions' => '.for-new-region',
                                               default => ''
                                           } }}"
                                           {{ old('shipping_method_id', $shippingMethods->first()->id) == $method->id ? 'checked' : '' }}>
                                    <label for="shipping_{{ $method->id }}">{{ $method->name }}</label>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Самовывоз --}}
                        <div class="local" style="{{ old('shipping_method_id', $shippingMethods->first()->code) === 'pickup' || !old('shipping_method_id') ? 'display:block' : 'display:none' }}">
                            <p><small>Адрес</small></p>
                            <p>г. Москва, Остаповский проезд, дом 5, строение 10.</p>
                            <p><small>Время работы</small></p>
                            <p>Ежедневно 08:00-19:00</p>
                        </div>

                        {{-- Москва и МО --}}
                        <div class="shipping" style="display:none">
                            <h3>Стоимость доставки по Москве и МО</h3>
                            <div class="line">
                                <div>
                                    <p>Если заказ < 3000 ₽</p>
                                </div>
                                <div>
                                    <p>Заказ отправляется почтой.<br>от 500 ₽</p>
                                </div>
                            </div>
                            <div class="line">
                                <div>
                                    <p>Если заказ > 3000 ₽</p>
                                </div>
                                <div>
                                    <p>Заказ отправляется курьерской службой.</p>
                                    <p><b>В пределах МКАД</b><br>500 ₽<br>В течение двух рабочих дней.</p>
                                    <p><b>По Новой Москве</b><br>550 ₽<br>В течение двух рабочих дней.</p>
                                    <p><b>По МО за МКАД</b><br>500 ₽ + 15 ₽ за километр.<br>В течение трёх рабочих дней.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Освобождённые регионы --}}
                        <div class="for-new-region" style="display:none">
                            <h2>Выберите регион</h2>
                            <div class="radio-wrapper">
                                @foreach (['Луганск', 'Донецк', 'Мариуполь', 'Бердянск', 'Мелитополь'] as $region)
                                    <label>
                                        <input value="{{ $region }}" type="radio" name="delivery_region"
                                               {{ old('delivery_region') === $region ? 'checked' : '' }}>
                                        <div class="decor"></div>
                                        <div class="text">{{ $region }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Россия — выбор ТК --}}
                        <div class="for-russia" style="display:none">
                            <h2>Выберите Транспортную компанию</h2>
                            <div class="radio-wrapper">
                                @foreach ($deliveryCompanies as $company)
                                    <label>
                                        <input value="{{ $company->id }}" type="radio" name="delivery_company_id"
                                               data-code="{{ $company->code }}"
                                               {{ old('delivery_company_id') == $company->id ? 'checked' : '' }}>
                                        <div class="decor"></div>
                                        <div class="text">{{ $company->name }}</div>
                                    </label>
                                @endforeach
                            </div>

                            {{-- СДЭК карта (показывается только при выборе СДЭК) --}}
                            <div id="cdek-block" style="display:none;">
                                <div id="cdek-map" style="height:400px; margin-bottom:20px; border-radius:10px; overflow:hidden; border:1px solid #d1d1d1;"></div>
                                <p class="cdek-selected-info" style="display:none; margin-bottom:15px; padding:10px; background:#f7f7f6; border-radius:8px;"></p>
                            </div>

                            {{-- Инфо для остальных ТК --}}
                            <div id="other-tk-info" class="checkout-info">
                                <h3>Стоимость доставки</h3>
                                <p>Стоимость доставки рассчитывается отдельно оператором.</p>
                            </div>
                        </div>

                        {{-- Данные получателя — 2 строки по 2 --}}
                        <h2>Данные получателя</h2>
                        <div class="billing_info">
                            <div class="form-field">
                                <input type="text" class="input-text" name="customer_first_name" id="billing_first_name"
                                       value="{{ old('customer_first_name') }}" required autocomplete="given-name">
                                <label for="billing_first_name">Имя</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                            <div class="form-field">
                                <input type="text" class="input-text" name="customer_last_name" id="billing_last_name"
                                       value="{{ old('customer_last_name') }}" required autocomplete="family-name">
                                <label for="billing_last_name">Фамилия</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                        </div>
                        <div class="billing_info">
                            <div class="form-field">
                                <input type="tel" class="input-text" name="customer_phone" id="billing_phone"
                                       value="{{ old('customer_phone') }}" required autocomplete="tel">
                                <label for="billing_phone">Телефон</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                            <div class="form-field">
                                <input type="email" class="input-text" name="customer_email" id="billing_email"
                                       value="{{ old('customer_email', auth()->user()?->email) }}" required autocomplete="email">
                                <label for="billing_email">E-mail</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                        </div>

                        {{-- Адрес доставки — Россия --}}
                        <div class="for-russia" style="display:none">
                            <h2>Адрес доставки</h2>
                            <div class="billing_info">
                                <div>
                                    <input type="text" class="input-text" name="customer_city"
                                           value="{{ old('customer_city') }}" placeholder="Город">
                                </div>
                                <div>
                                    <input type="text" class="input-text" name="customer_postal_code"
                                           value="{{ old('customer_postal_code') }}" placeholder="Индекс">
                                </div>
                            </div>
                            <div class="text-info">
                                <div>
                                    <input type="text" class="input-text" name="customer_address_line_1"
                                           value="{{ old('customer_address_line_1') }}" placeholder="Адрес доставки">
                                </div>
                            </div>
                            <div class="text-info">
                                <div>
                                    <textarea class="input-text" name="comment" placeholder="Комментарий к заказу" rows="3" style="height:auto;padding-top:15px;">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Адрес доставки — Москва и МО --}}
                        <div class="shipping" style="display:none">
                            <h2>Адрес доставки</h2>
                            <div class="billing_info">
                                <div>
                                    <input type="text" class="input-text" name="customer_city"
                                           value="{{ old('customer_city') }}" placeholder="Город">
                                </div>
                                <div>
                                    <input type="text" class="input-text" name="customer_postal_code"
                                           value="{{ old('customer_postal_code') }}" placeholder="Индекс">
                                </div>
                            </div>
                            <div class="text-info">
                                <div>
                                    <input type="text" class="input-text" name="customer_address_line_1"
                                           value="{{ old('customer_address_line_1') }}" placeholder="Адрес доставки">
                                </div>
                            </div>
                            <div class="shipping_info">
                                <div>
                                    <input type="text" class="input-text" name="shipping_entrance" placeholder="Подъезд"
                                           value="{{ old('shipping_entrance') }}">
                                </div>
                                <div>
                                    <input type="text" class="input-text" name="shipping_flat" placeholder="Квартира"
                                           value="{{ old('shipping_flat') }}">
                                </div>
                                <div>
                                    <input type="text" class="input-text" name="shipping_floor" placeholder="Этаж"
                                           value="{{ old('shipping_floor') }}">
                                </div>
                            </div>
                            <div class="text-info">
                                <div>
                                    <textarea class="input-text" name="comment" placeholder="Комментарий к заказу" rows="3" style="height:auto;padding-top:15px;">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Адрес доставки — Освобождённые регионы --}}
                        <div class="for-new-region" style="display:none">
                            <h2>Адрес доставки</h2>
                            <div class="text-info">
                                <div>
                                    <input type="text" class="input-text" name="customer_address_line_1"
                                           value="{{ old('customer_address_line_1') }}" placeholder="Адрес доставки">
                                </div>
                            </div>
                            <div class="text-info">
                                <div>
                                    <textarea class="input-text" name="comment" placeholder="Комментарий к заказу" rows="3" style="height:auto;padding-top:15px;">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="customer_country" value="RU">
                        <input type="hidden" name="payment_method" value="cash_on_delivery">
                        <input type="hidden" name="cdek_pvz_code" id="cdek_pvz_code" value="">
                        <input type="hidden" name="cdek_tariff_id" id="cdek_tariff_id" value="">
                        <input type="hidden" name="cdek_delivery_cost" id="cdek_delivery_cost" value="0">
                        <input type="hidden" name="cdek_city_code" id="cdek_city_code" value="">
                    </div>

                    {{-- Боковая панель --}}
                    <div>
                        <div class="totals">
                            <h3>Ваша корзина</h3>
                            <div>
                                <p class="items-text">Товары ({{ $cart['total_quantity'] }})</p>
                                <p class="items-price">{{ number_format($cart['subtotal'], 0, '', ' ') }} ₽</p>
                            </div>
                            @if ($cart['discount'] > 0)
                                <hr>
                                <div class="discount">
                                    <p class="items-text">Выгода</p>
                                    <p class="items-price">-{{ number_format($cart['discount'], 0, '', ' ') }} ₽</p>
                                </div>
                            @endif
                            <hr>
                            <div>
                                <p class="total-text">Итого к оплате</p>
                                <p class="total-price">{{ number_format($cart['total'], 0, '', ' ') }} ₽</p>
                            </div>
                            <button type="submit" class="basket__btn checkout-button wc-forward">Оплатить</button>
                            <p>Нажимая на кнопку, вы соглашаетесь с <a href="{{ route('page.policy') }}">Условиями обработки перс. данных</a></p>
                        </div>
                    </div>
                </div>

            </form>
        @endif
    </section>
@endsection

@push('scripts')
<script>
    window.CDEK_SERVICE_URL = '/api/cdek';
    window.CHECKOUT_SUBTOTAL = {{ $cart['total'] ?? 0 }};
</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@cdek-it/widget@3" charset="utf-8"></script>
<script src="https://unpkg.com/imask"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ======== СДЭК виджет (определяем ДО всех обработчиков) ========
    var cdekWidgetInstance = null;

    function initCdekWidget() {
        var container = document.getElementById('cdek-map');
        if (!container) return;
        if (cdekWidgetInstance) return;

        if (typeof CDEKWidget === 'undefined') {
            // Скрипт СДЭК ещё не загрузился — ждём
            setTimeout(initCdekWidget, 300);
            return;
        }

        cdekWidgetInstance = new CDEKWidget({
            from: 'Москва',
            root: 'cdek-map',
            apiKey: '',
            servicePath: window.CDEK_SERVICE_URL || '/api/cdek',
            defaultLocation: 'Москва',
            lang: 'rus',
            currency: 'RUB',
            tariffs: {
                office: [234, 136, 138],
                door: [233, 137, 139],
            },
            onChoose: function(delivery, rate, address) {
                document.getElementById('cdek_pvz_code').value = delivery.code || '';
                document.getElementById('cdek_tariff_id').value = rate.tariff_code || '';
                document.getElementById('cdek_delivery_cost').value = rate.delivery_sum || 0;
                document.getElementById('cdek_city_code').value = delivery.city_code || '';

                var info = document.querySelector('.cdek-selected-info');
                if (info) {
                    info.style.display = 'block';
                    info.textContent = (address.name || delivery.name || 'ПВЗ') + ' — ' + Math.round(rate.delivery_sum) + ' ₽';
                }

                updateCheckoutTotal(rate.delivery_sum);
            }
        });
    }

    function updateCheckoutTotal(deliveryCost) {
        var subtotal = window.CHECKOUT_SUBTOTAL || 0;
        var cost = parseFloat(deliveryCost) || 0;
        var totalEl = document.querySelector('.total-price');
        if (totalEl) {
            totalEl.textContent = Math.round(subtotal + cost).toLocaleString('ru-RU') + ' ₽';
        }
    }

    function resetCdekFields() {
        ['cdek_pvz_code', 'cdek_tariff_id', 'cdek_city_code'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        var costEl = document.getElementById('cdek_delivery_cost');
        if (costEl) costEl.value = '0';
        var info = document.querySelector('.cdek-selected-info');
        if (info) info.style.display = 'none';
        updateCheckoutTotal(0);
    }

    function handleDeliveryCompanyChange(radio) {
        var cdekBlock = document.getElementById('cdek-block');
        var otherInfo = document.getElementById('other-tk-info');

        if (radio.dataset.code === 'cdek') {
            if (cdekBlock) cdekBlock.style.display = 'block';
            if (otherInfo) otherInfo.style.display = 'none';
            setTimeout(initCdekWidget, 200);
        } else {
            if (cdekBlock) cdekBlock.style.display = 'none';
            if (otherInfo) otherInfo.style.display = 'block';
            resetCdekFields();
        }
    }

    // ======== Маска телефона ========
    var phoneEl = document.getElementById('billing_phone');
    if (phoneEl && typeof IMask !== 'undefined') {
        IMask(phoneEl, { mask: '+{7}(000)000-00-00' });
    }

    // ======== Переключение блоков доставки ========
    function hideAllBlocks() {
        document.querySelectorAll('.local, .shipping, .for-russia, .for-new-region').forEach(function(block) {
            block.style.display = 'none';
        });
    }

    document.querySelectorAll('.shipping_method').forEach(function(radio) {
        radio.addEventListener('change', function() {
            hideAllBlocks();
            var targetClass = this.dataset.target;
            if (targetClass) {
                document.querySelectorAll(targetClass).forEach(function(el) {
                    el.style.display = 'block';
                });
            } else {
                document.querySelectorAll('.local').forEach(function(el) {
                    el.style.display = 'block';
                });
            }

            // При переходе на «Россия» — проверить выбранную ТК
            if (this.dataset.code === 'free_russia') {
                var checkedCompany = document.querySelector('[name="delivery_company_id"]:checked');
                if (checkedCompany) {
                    handleDeliveryCompanyChange(checkedCompany);
                }
            } else {
                resetCdekFields();
            }
        });

        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });

    // ======== Обработчики ТК ========
    document.querySelectorAll('[name="delivery_company_id"]').forEach(function(el) {
        el.addEventListener('change', function() {
            handleDeliveryCompanyChange(this);
        });
    });

    // ======== Валидация формы ========
    var checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            var requiredFields = {
                'customer_first_name': 'Имя',
                'customer_last_name': 'Фамилия',
                'customer_phone': 'Телефон',
                'customer_email': 'Email'
            };

            var hasErrors = false;
            var errorMessage = 'Пожалуйста, заполните следующие поля:\n';

            for (var fieldName in requiredFields) {
                var fieldLabel = requiredFields[fieldName];
                var field = document.querySelector('input[name="' + fieldName + '"]');
                if (!field || !field.value.trim()) {
                    hasErrors = true;
                    errorMessage += '- ' + fieldLabel + '\n';
                    if (field) {
                        field.classList.add('error');
                        var errorText = field.parentNode.querySelector('.error-text');
                        if (errorText) errorText.style.display = 'block';
                    }
                } else {
                    if (field) {
                        field.classList.remove('error');
                        var errorText = field.parentNode.querySelector('.error-text');
                        if (errorText) errorText.style.display = 'none';
                    }
                }
            }

            var emailField = document.querySelector('input[name="customer_email"]');
            if (emailField && emailField.value.trim()) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value.trim())) {
                    hasErrors = true;
                    errorMessage += '- Некорректный формат email\n';
                    emailField.classList.add('error');
                }
            }

            if (hasErrors) {
                e.preventDefault();
                alert(errorMessage);
                var submitButton = document.querySelector('.checkout-button');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Оплатить';
                }
                return false;
            }

            var submitButton = document.querySelector('.checkout-button');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Обработка...';
                setTimeout(function() {
                    if (submitButton.disabled) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Оплатить';
                    }
                }, 10000);
            }

            return true;
        });
    }
});
</script>
@endpush
