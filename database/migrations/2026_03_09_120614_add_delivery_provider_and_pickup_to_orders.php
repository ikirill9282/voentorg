<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_provider')->nullable()->after('delivery_region');
            $table->foreignId('pickup_store_id')->nullable()->after('delivery_provider')->constrained('stores')->nullOnDelete();
            $table->boolean('pickup_prepaid')->default(false)->after('pickup_store_id');
            $table->integer('pickup_estimated_days')->nullable()->after('pickup_prepaid');
            $table->string('yandex_claim_id')->nullable()->after('cdek_tracking_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_store_id']);
            $table->dropColumn(['delivery_provider', 'pickup_store_id', 'pickup_prepaid', 'pickup_estimated_days', 'yandex_claim_id']);
        });
    }
};
