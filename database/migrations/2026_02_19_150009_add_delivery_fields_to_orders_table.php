<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_company_id')->nullable()->after('shipping_method_id')->constrained()->nullOnDelete();
            $table->string('delivery_region')->nullable()->after('delivery_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_company_id']);
            $table->dropColumn(['delivery_company_id', 'delivery_region']);
        });
    }
};
