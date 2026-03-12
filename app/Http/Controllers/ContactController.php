<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Rules\YandexCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'smart-token' => config('services.yandex_captcha.server_key') ? ['required', new YandexCaptcha] : [],
        ]);

        unset($validated['smart-token']);
        ContactSubmission::query()->create($validated);

        return redirect()->back()->with('contact_success', 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
    }
}
