<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'patronymic',
        'callsign',
        'birthday',
        'telegram_username',
        'phone',
        'email',
        'password',
        'is_admin',
        'external_id',
        'bonus_balance',
        'total_spent',
        'loyalty_tier',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'birthday' => 'date',
            'bonus_balance' => 'decimal:2',
            'total_spent' => 'decimal:2',
            'loyalty_tier' => 'integer',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function bonusTransactions(): HasMany
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name} {$this->first_name} {$this->patronymic}");
    }

    public function getLoyaltyPercentage(): int
    {
        return match ((int) $this->loyalty_tier) {
            2 => 7,
            3 => 10,
            4 => 15,
            default => 5,
        };
    }

    public function getAvailableBonusForOrder(float $orderTotal): float
    {
        return min((float) $this->bonus_balance, $orderTotal * 0.5);
    }
}
