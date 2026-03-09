<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('external_id', 36)->nullable()->unique()->after('id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('external_id', 36)->nullable()->unique()->after('id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('external_id', 36)->nullable()->unique()->after('id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('external_id', 36)->nullable()->unique()->after('id');
            $table->string('external_status')->nullable()->after('status');
            $table->timestamp('commerceml_exported_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'external_status', 'commerceml_exported_at']);
        });
    }
};
