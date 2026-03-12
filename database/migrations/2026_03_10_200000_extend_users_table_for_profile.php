<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('patronymic')->nullable()->after('last_name');
            $table->string('callsign')->nullable()->after('patronymic');
            $table->date('birthday')->nullable()->after('callsign');
            $table->string('telegram_username')->nullable()->unique()->after('birthday');
            $table->string('phone')->nullable()->unique()->after('telegram_username');
            $table->string('external_id', 36)->nullable()->unique()->after('phone');
            $table->decimal('bonus_balance', 12, 2)->default(0)->after('external_id');
            $table->decimal('total_spent', 12, 2)->default(0)->after('bonus_balance');
            $table->tinyInteger('loyalty_tier')->default(1)->after('total_spent');
        });

        // Make email nullable for phone-only registrations
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'patronymic', 'callsign',
                'birthday', 'telegram_username', 'phone', 'external_id',
                'bonus_balance', 'total_spent', 'loyalty_tier',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
