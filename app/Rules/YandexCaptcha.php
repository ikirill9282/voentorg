<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class YandexCaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $serverKey = config('services.yandex_captcha.server_key');

        if (! $serverKey) {
            return; // Skip if not configured
        }

        if (! $value) {
            $fail('Проверка капчи не пройдена.');
            return;
        }

        $response = Http::get('https://smartcaptcha.yandexcloud.net/validate', [
            'secret' => $serverKey,
            'token' => $value,
            'ip' => request()->ip(),
        ]);

        $data = $response->json();

        if (($data['status'] ?? '') !== 'ok') {
            $fail('Проверка капчи не пройдена.');
        }
    }
}
