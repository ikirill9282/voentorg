<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim($this->string('login'));
        $password = $this->string('password');

        // Determine which field to use for lookup
        $user = $this->findUser($login);

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    private function findUser(string $login): ?User
    {
        // Starts with @ → telegram
        if (str_starts_with($login, '@')) {
            $username = ltrim($login, '@');

            return User::query()->where('telegram_username', $username)->first();
        }

        // Contains @ and dot → email
        if (str_contains($login, '@') && str_contains($login, '.')) {
            return User::query()->where('email', Str::lower($login))->first();
        }

        // Otherwise → phone (normalize: keep only digits and +)
        $phone = $login;

        // Try exact match first
        $user = User::query()->where('phone', $phone)->first();
        if ($user) {
            return $user;
        }

        // Try normalized match (digits only)
        $digits = preg_replace('/[^\d+]/', '', $phone);

        return User::query()->where('phone', 'LIKE', "%{$digits}%")->first();
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')) . '|' . $this->ip());
    }
}
