@if (config('services.yandex_captcha.client_key'))
    <div id="yandex-captcha-{{ $id ?? 'default' }}" class="smart-captcha"
         data-sitekey="{{ config('services.yandex_captcha.client_key') }}"
         data-hl="ru"></div>
    <input type="hidden" name="smart-token" class="smart-captcha-token">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var container = document.getElementById('yandex-captcha-{{ $id ?? "default" }}');
            if (!container || container.dataset.scInit) return;
            container.dataset.scInit = '1';
            var input = container.parentElement.querySelector('.smart-captcha-token');
            if (typeof window.smartCaptcha !== 'undefined') {
                window.smartCaptcha.render(container, {
                    sitekey: '{{ config('services.yandex_captcha.client_key') }}',
                    hl: 'ru',
                    callback: function (token) {
                        if (input) input.value = token;
                    }
                });
            }
        });
    </script>
@endif
