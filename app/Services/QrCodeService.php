<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateForUser(User $user): string
    {
        if (! $user->external_id) {
            $user->update(['external_id' => (string) Str::uuid()]);
        }

        $payload = json_encode([
            'type' => 'colchuga_loyalty',
            'user_id' => $user->external_id,
        ]);

        return QrCode::format('svg')
            ->size(250)
            ->margin(1)
            ->generate($payload);
    }
}
