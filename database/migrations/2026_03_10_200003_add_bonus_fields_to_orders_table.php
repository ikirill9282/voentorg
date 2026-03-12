<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('bonus_used', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('bonus_earned', 12, 2)->default(0)->after('bonus_used');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['bonus_used', 'bonus_earned']);
        });
    }
};
