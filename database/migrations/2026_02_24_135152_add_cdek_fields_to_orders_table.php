<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cdek_uuid')->nullable()->after('comment');
            $table->string('cdek_tracking_number')->nullable()->after('cdek_uuid');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('shipping_total');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cdek_uuid', 'cdek_tracking_number', 'discount_amount']);
        });
    }
};
