<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],
            'callsign' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'telegram_username' => ['nullable', 'string', 'max:255', 'unique:users,telegram_username'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:80', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Normalize telegram username — strip @ if provided
        $telegram = $validated['telegram_username'] ?? null;
        if ($telegram) {
            $telegram = ltrim($telegram, '@');
        }

        $user = User::create([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'patronymic' => $validated['patronymic'] ?? null,
            'callsign' => $validated['callsign'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'telegram_username' => $telegram,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('account.dashboard', absolute: false));
    }
}
