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
                        <h2>Способ доставки</h2>
                        <ul id="shipping_method" class="woocommerce-shipping-methods">
                            @foreach ($shippingMethods as $method)
                                <li>
                                    <input type="radio" name="shipping_method_id" id="shipping_{{ $method->id }}"
                                           value="{{ $method->id }}" class="shipping_method"
                                           data-code="{{ $method->code }}"
                                           data-price="{{ $method->price }}"
                                           {{ old('shipping_method_id', $shippingMethods->first()->id) == $method->id ? 'checked' : '' }}>
                                    <label for="shipping_{{ $method->id }}">{{ $method->name }}</label>
                                </li>
                            @endforeach
                        </ul>

                        {{-- ============================================================ --}}
                        {{-- Самовывоз --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--pickup" data-method="pickup" style="display:none">
                            @if ($stores->isNotEmpty())
                                <h3>Выберите магазин</h3>
                                <div class="pickup-stores">
                                    @foreach ($stores as $store)
                                        <label class="pickup-store-card">
                                            <input type="radio" name="pickup_store_id" value="{{ $store->id }}"
                                                   data-store-id="{{ $store->id }}"
                                                   {{ old('pickup_store_id') == $store->id ? 'checked' : '' }}>
                                            <div class="pickup-store-card__inner">
                                                <div class="pickup-store-card__name">{{ $store->name }}</div>
                                                <div class="pickup-store-card__address">{{ $store->address }}{{ $store->city ? ', ' . $store->city : '' }}</div>
                                                @if ($store->phone)
                                                    <div class="pickup-store-card__phone">{{ $store->phone }}</div>
                                                @endif
                                                <div class="pickup-store-card__availability" data-store-availability="{{ $store->id }}"></div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="pickup-payment-options" style="display:none">
                                    <h3>Вариант оплаты</h3>
                                    <div class="radio-wrapper">
                                        <label>
                                            <input type="radio" name="pickup_prepaid" value="1" {{ old('pickup_prepaid', '1') == '1' ? 'checked' : '' }}>
                                            <div class="decor"></div>
                                            <div class="text">С предоплатой 100%</div>
                                        </label>
                                        <label>
                                            <input type="radio" name="pickup_prepaid" value="0" {{ old('pickup_prepaid') === '0' ? 'checked' : '' }}>
                                            <div class="decor"></div>
                                            <div class="text">Без предоплаты (резерв на 3 дня)</div>
                                        </label>
                                    </div>
                                    <p class="pickup-note">Хранение 3 дня. Для продления свяжитесь с консультантом.</p>
                                </div>
                            @else
                                <p>г. Москва, Остаповский проезд, дом 5, строение 10.</p>
                                <p><small>Время работы: Ежедневно 08:00-19:00</small></p>
                            @endif
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Москва и МО --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--moscow" data-method="free_moscow" style="display:none">
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

                        {{-- ============================================================ --}}
                        {{-- По России (СДЭК + другие ТК) --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--russia" data-method="free_russia" style="display:none">
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

                            {{-- СДЭК карта --}}
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

                        {{-- ============================================================ --}}
                        {{-- Освобождённые регионы --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--regions" data-method="free_regions" style="display:none">
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

                        {{-- ============================================================ --}}
                        {{-- OZON --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--ozon" data-method="ozon" style="display:none">
                            <h3>Доставка OZON</h3>
                            <p class="delivery-operator-note">Стоимость доставки рассчитывается оператором после оформления заказа.</p>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Яндекс Доставка --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--yandex" data-method="yandex" style="display:none">
                            <h3>Яндекс Доставка</h3>
                            <div class="yandex-estimate-result" style="display:none;"></div>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Почта России --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-block delivery-block--pochta" data-method="pochta" style="display:none">
                            <h3>Почта России</h3>
                            <p class="delivery-operator-note">Стоимость доставки Почтой России рассчитывается оператором после оформления заказа.</p>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Сохранённые адреса (для авторизованных) --}}
                        {{-- ============================================================ --}}
                        @auth
                            @if ($savedAddresses->isNotEmpty())
                                <div class="saved-addresses-block">
                                    <h2>Сохранённые адреса</h2>
                                    <div class="saved-addresses">
                                        @foreach ($savedAddresses as $addr)
                                            <label class="saved-address-card">
                                                <input type="radio" name="saved_address_id" value="{{ $addr->id }}"
                                                       data-city="{{ $addr->city }}"
                                                       data-postal="{{ $addr->postal_code }}"
                                                       data-address1="{{ $addr->address_line_1 }}"
                                                       data-address2="{{ $addr->address_line_2 }}"
                                                       data-region="{{ $addr->region }}"
                                                       data-shipping="{{ $addr->shipping_method_id }}">
                                                <div class="saved-address-card__inner">
                                                    <div class="saved-address-card__label">{{ $addr->label ?: 'Адрес ' . $loop->iteration }}</div>
                                                    <div class="saved-address-card__text">{{ $addr->city }}{{ $addr->postal_code ? ', ' . $addr->postal_code : '' }}, {{ $addr->address_line_1 }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                        <label class="saved-address-card">
                                            <input type="radio" name="saved_address_id" value="" checked>
                                            <div class="saved-address-card__inner">
                                                <div class="saved-address-card__label">Новый адрес</div>
                                                <div class="saved-address-card__text">Ввести вручную</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- ============================================================ --}}
                        {{-- Данные получателя --}}
                        {{-- ============================================================ --}}
                        <h2>Данные получателя</h2>
                        <div class="billing_info">
                            <div class="form-field">
                                <input type="text" class="input-text" name="customer_first_name" id="billing_first_name"
                                       value="{{ old('customer_first_name', auth()->user()?->first_name) }}" required autocomplete="given-name">
                                <label for="billing_first_name">Имя</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                            <div class="form-field">
                                <input type="text" class="input-text" name="customer_last_name" id="billing_last_name"
                                       value="{{ old('customer_last_name', auth()->user()?->last_name) }}" required autocomplete="family-name">
                                <label for="billing_last_name">Фамилия</label>
                                <p class="error-text">Данное поле обязательно для заполнения</p>
                            </div>
                        </div>
                        <div class="billing_info">
                            <div class="form-field">
                                <input type="tel" class="input-text" name="customer_phone" id="billing_phone"
                                       value="{{ old('customer_phone', auth()->user()?->phone) }}" required autocomplete="tel">
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

                        {{-- ============================================================ --}}
                        {{-- Адрес доставки — для методов с адресом --}}
                        {{-- ============================================================ --}}
                        <div class="delivery-address-block" data-for-methods="free_russia,free_moscow,ozon,yandex,pochta,free_regions" style="display:none">
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

                            {{-- Дополнительные поля для Москвы --}}
                            <div class="moscow-extra-fields" style="display:none">
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
                            </div>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Способ оплаты --}}
                        {{-- ============================================================ --}}
                        <h2>Способ оплаты</h2>
                        <div class="radio-wrapper">
                            <label>
                                <input type="radio" name="payment_method" value="online_payment"
                                       {{ old('payment_method', 'online_payment') === 'online_payment' ? 'checked' : '' }}>
                                <div class="decor"></div>
                                <div class="text">Оплатить онлайн <small>(Visa, MasterCard, МИР)</small></div>
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="cash_on_delivery"
                                       {{ old('payment_method') === 'cash_on_delivery' ? 'checked' : '' }}>
                                <div class="decor"></div>
                                <div class="text">Оплата при получении</div>
                            </label>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- Комментарий --}}
                        {{-- ============================================================ --}}
                        <div class="text-info" style="margin-top:15px;">
                            <div>
                                <textarea class="input-text" name="comment" placeholder="Комментарий к заказу" rows="3" style="height:auto;padding-top:15px;">{{ old('comment') }}</textarea>
                            </div>
                        </div>

                        <input type="hidden" name="customer_country" value="RU">
                        <input type="hidden" name="delivery_provider" id="delivery_provider" value="">
                        <input type="hidden" name="cdek_pvz_code" id="cdek_pvz_code" value="">
                        <input type="hidden" name="cdek_tariff_id" id="cdek_tariff_id" value="">
                        <input type="hidden" name="cdek_delivery_cost" id="cdek_delivery_cost" value="0">
                        <input type="hidden" name="cdek_city_code" id="cdek_city_code" value="">
                        <input type="hidden" name="yandex_delivery_cost" id="yandex_delivery_cost" value="0">
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
                            <div class="delivery-cost-line" style="display:none;">
                                <hr>
                                <div>
                                    <p class="items-text">Доставка</p>
                                    <p class="items-price delivery-cost-value">0 ₽</p>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <p class="total-text">Итого к оплате</p>
                                <p class="total-price">{{ number_format($cart['total'], 0, '', ' ') }} ₽</p>
                            </div>
                            @auth
                                @if ($bonusInfo)
                                    <div class="checkout-bonus">
                                        <hr>
                                        <div class="checkout-bonus__header">
                                            <p class="checkout-bonus__title">Бонусы</p>
                                            <span class="checkout-bonus__balance">{{ number_format($bonusInfo['balance'], 0, '', ' ') }} &#8381;</span>
                                        </div>
                                        <div class="checkout-bonus__input-row">
                                            <input type="number" name="bonus_amount" id="bonus_amount"
                                                   class="input-text checkout-bonus__input"
                                                   min="0" max="{{ $bonusInfo['max_redeemable'] }}"
                                                   step="1" value="0" placeholder="0">
                                            <button type="button" class="checkout-bonus__max" id="bonus_max_btn">Макс.</button>
                                        </div>
                                        <p class="checkout-bonus__hint">Можно списать до {{ number_format($bonusInfo['max_redeemable'], 0, '', ' ') }} &#8381; (до 50% заказа)</p>
                                        <hr>
                                    </div>
                                @endif
                            @endauth
                            @include('store.partials.captcha-field', ['id' => 'checkout'])
                            <button type="submit" class="basket__btn checkout-button wc-forward">Оформить заказ</button>
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

    // ======== СДЭК виджет ========
    var cdekWidgetInstance = null;

    function initCdekWidget() {
        var container = document.getElementById('cdek-map');
        if (!container) return;
        if (cdekWidgetInstance) return;

        if (typeof CDEKWidget === 'undefined') {
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

                updateDeliveryCost(rate.delivery_sum);
            }
        });
    }

    // ======== Итоговая стоимость ========
    function updateDeliveryCost(cost) {
        var subtotal = window.CHECKOUT_SUBTOTAL || 0;
        cost = parseFloat(cost) || 0;

        var costLine = document.querySelector('.delivery-cost-line');
        var costValue = document.querySelector('.delivery-cost-value');
        var totalEl = document.querySelector('.total-price');

        if (cost > 0) {
            if (costLine) costLine.style.display = 'block';
            if (costValue) costValue.textContent = Math.round(cost).toLocaleString('ru-RU') + ' ₽';
        } else {
            if (costLine) costLine.style.display = 'none';
        }

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
            updateDeliveryCost(0);
        }
    }

    // ======== Маска телефона ========
    var phoneEl = document.getElementById('billing_phone');
    if (phoneEl && typeof IMask !== 'undefined') {
        IMask(phoneEl, { mask: '+{7}(000)000-00-00' });
    }

    // ======== Переключение блоков доставки ========
    function hideAllDeliveryBlocks() {
        document.querySelectorAll('.delivery-block').forEach(function(block) {
            block.style.display = 'none';
        });
        document.querySelector('.delivery-address-block').style.display = 'none';
        document.querySelector('.moscow-extra-fields').style.display = 'none';
    }

    function showDeliveryBlock(methodCode) {
        hideAllDeliveryBlocks();
        resetCdekFields();
        updateDeliveryCost(0);

        // Update delivery_provider hidden field
        var providerEl = document.getElementById('delivery_provider');
        if (providerEl) providerEl.value = methodCode;

        // Show the matching delivery block
        var block = document.querySelector('.delivery-block[data-method="' + methodCode + '"]');
        if (block) block.style.display = 'block';

        // Show address block for methods that need it
        var addressBlock = document.querySelector('.delivery-address-block');
        var methodsWithAddress = (addressBlock.dataset.forMethods || '').split(',');
        if (methodsWithAddress.indexOf(methodCode) !== -1) {
            addressBlock.style.display = 'block';

            // Moscow extra fields
            if (methodCode === 'free_moscow') {
                document.querySelector('.moscow-extra-fields').style.display = 'block';
            }
        }

        // Handle delivery company selection for Russia
        if (methodCode === 'free_russia') {
            var checkedCompany = document.querySelector('[name="delivery_company_id"]:checked');
            if (checkedCompany) {
                handleDeliveryCompanyChange(checkedCompany);
            }
        }

        // Reset yandex cost
        var yandexCostEl = document.getElementById('yandex_delivery_cost');
        if (yandexCostEl) yandexCostEl.value = '0';
    }

    document.querySelectorAll('.shipping_method').forEach(function(radio) {
        radio.addEventListener('change', function() {
            showDeliveryBlock(this.dataset.code);
        });

        if (radio.checked) {
            showDeliveryBlock(radio.dataset.code);
        }
    });

    // ======== Обработчики ТК ========
    document.querySelectorAll('[name="delivery_company_id"]').forEach(function(el) {
        el.addEventListener('change', function() {
            handleDeliveryCompanyChange(this);
        });
    });

    // ======== Самовывоз — выбор магазина ========
    document.querySelectorAll('[name="pickup_store_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var storeId = this.value;

            // Show payment options
            var paymentOpts = document.querySelector('.pickup-payment-options');
            if (paymentOpts) paymentOpts.style.display = 'block';

            // Check availability
            var availabilityEl = document.querySelector('[data-store-availability="' + storeId + '"]');
            if (availabilityEl) {
                availabilityEl.innerHTML = '<span style="color:#888;">Проверяем наличие...</span>';
            }

            fetch('/api/stores/' + storeId + '/cart-availability', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!availabilityEl) return;

                if (data.all_in_stock) {
                    availabilityEl.innerHTML = '<span class="pickup-stock pickup-stock--yes">Все товары в наличии</span>';
                } else {
                    var html = '<span class="pickup-stock pickup-stock--partial">Не все товары в наличии. Доставка с производства ~5 рабочих дней.</span>';
                    if (data.items && data.items.length) {
                        html += '<ul class="pickup-stock-list">';
                        data.items.forEach(function(item) {
                            var icon = item.in_stock ? '&#10003;' : '&#10007;';
                            var cls = item.in_stock ? 'yes' : 'no';
                            html += '<li class="pickup-stock-item pickup-stock-item--' + cls + '">' + icon + ' ' + item.name + '</li>';
                        });
                        html += '</ul>';
                    }
                    availabilityEl.innerHTML = html;
                }
            })
            .catch(function() {
                if (availabilityEl) {
                    availabilityEl.innerHTML = '<span style="color:#888;">Не удалось проверить наличие</span>';
                }
            });
        });

        // If already checked on load
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });

    // ======== Яндекс — расчёт стоимости ========
    var yandexEstimateTimer = null;
    function estimateYandexDelivery() {
        var currentMethod = document.querySelector('.shipping_method:checked');
        if (!currentMethod || currentMethod.dataset.code !== 'yandex') return;

        var city = document.querySelector('[name="customer_city"]');
        var address = document.querySelector('[name="customer_address_line_1"]');
        if (!city || !address || !city.value.trim() || !address.value.trim()) return;

        var fullAddress = city.value.trim() + ', ' + address.value.trim();
        var resultEl = document.querySelector('.yandex-estimate-result');
        if (resultEl) {
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<span style="color:#888;">Рассчитываем стоимость...</span>';
        }

        fetch('/api/yandex-delivery/estimate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ address: fullAddress })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!resultEl) return;
            if (data.success && data.price) {
                var price = parseFloat(data.price);
                resultEl.innerHTML = '<span class="yandex-price">Стоимость доставки: ' + Math.round(price).toLocaleString('ru-RU') + ' ₽</span>';
                document.getElementById('yandex_delivery_cost').value = price;
                updateDeliveryCost(price);
            } else {
                resultEl.innerHTML = '<span class="delivery-operator-note">Не удалось рассчитать стоимость. Оператор уточнит после оформления.</span>';
                document.getElementById('yandex_delivery_cost').value = '0';
                updateDeliveryCost(0);
            }
        })
        .catch(function() {
            if (resultEl) {
                resultEl.innerHTML = '<span class="delivery-operator-note">Не удалось рассчитать стоимость. Оператор уточнит после оформления.</span>';
            }
            document.getElementById('yandex_delivery_cost').value = '0';
            updateDeliveryCost(0);
        });
    }

    // Debounced estimate on address change
    document.querySelectorAll('[name="customer_city"], [name="customer_address_line_1"]').forEach(function(el) {
        el.addEventListener('input', function() {
            clearTimeout(yandexEstimateTimer);
            yandexEstimateTimer = setTimeout(estimateYandexDelivery, 800);
        });
    });

    // ======== Сохранённые адреса ========
    document.querySelectorAll('[name="saved_address_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (!this.value) return; // "Новый адрес" — clear nothing
            var city = document.querySelector('[name="customer_city"]');
            var postal = document.querySelector('[name="customer_postal_code"]');
            var addr1 = document.querySelector('[name="customer_address_line_1"]');
            var addr2 = document.querySelector('[name="customer_address_line_2"]');
            var region = document.querySelector('[name="customer_region"]');

            if (city) city.value = this.dataset.city || '';
            if (postal) postal.value = this.dataset.postal || '';
            if (addr1) addr1.value = this.dataset.address1 || '';
            if (addr2) addr2.value = this.dataset.address2 || '';
            if (region) region.value = this.dataset.region || '';

            // Select matching shipping method if exists
            var shippingId = this.dataset.shipping;
            if (shippingId) {
                var shippingRadio = document.querySelector('.shipping_method[value="' + shippingId + '"]');
                if (shippingRadio && !shippingRadio.checked) {
                    shippingRadio.checked = true;
                    shippingRadio.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    // ======== Бонусы в чекауте ========
    var bonusInput = document.getElementById('bonus_amount');
    var bonusMaxBtn = document.getElementById('bonus_max_btn');

    if (bonusInput && bonusMaxBtn) {
        bonusMaxBtn.addEventListener('click', function() {
            bonusInput.value = bonusInput.max;
            bonusInput.dispatchEvent(new Event('input'));
        });

        bonusInput.addEventListener('input', function() {
            var val = parseFloat(this.value) || 0;
            var max = parseFloat(this.max) || 0;
            if (val > max) { this.value = max; val = max; }
            if (val < 0) { this.value = 0; val = 0; }

            // Recalculate total
            var subtotal = window.CHECKOUT_SUBTOTAL || 0;
            var deliveryCost = parseFloat(document.getElementById('cdek_delivery_cost')?.value || 0)
                + parseFloat(document.getElementById('yandex_delivery_cost')?.value || 0);
            var totalEl = document.querySelector('.total-price');
            if (totalEl) {
                totalEl.textContent = Math.round(subtotal + deliveryCost - val).toLocaleString('ru-RU') + ' ₽';
            }
        });
    }

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

            // Validate pickup store selection
            var currentMethod = document.querySelector('.shipping_method:checked');
            if (currentMethod && currentMethod.dataset.code === 'pickup') {
                var selectedStore = document.querySelector('[name="pickup_store_id"]:checked');
                if (!selectedStore) {
                    hasErrors = true;
                    errorMessage += '- Выберите магазин для самовывоза\n';
                }
            }

            if (hasErrors) {
                e.preventDefault();
                alert(errorMessage);
                var submitButton = document.querySelector('.checkout-button');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Оформить заказ';
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
                        submitButton.textContent = 'Оформить заказ';
                    }
                }, 10000);
            }

            return true;
        });
    }
});
</script>
@endpush
