<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],
            'callsign' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'telegram_username' => [
                'nullable', 'string', 'max:255',
                Rule::unique(User::class, 'telegram_username')->ignore($this->user()->id),
            ],
            'phone' => [
                'required', 'string', 'max:80',
                Rule::unique(User::class, 'phone')->ignore($this->user()->id),
            ],
            'email' => [
                'nullable', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class, 'email')->ignore($this->user()->id),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->telegram_username) {
            $this->merge([
                'telegram_username' => ltrim($this->telegram_username, '@'),
            ]);
        }
    }
}
