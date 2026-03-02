<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // type column is already a string in SQLite — 'free_product' is a new valid value
        // handled at application level, no column change needed

        Schema::table('coupons', function (Blueprint $table) {
            $table->text('description')->nullable()->after('code');
            $table->string('scope')->default('cart')->after('type');
            $table->unsignedBigInteger('free_product_id')->nullable()->after('scope');
            $table->decimal('max_discount', 12, 2)->nullable()->after('min_order_amount');

            $table->foreign('free_product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::create('coupon_product', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('product_id');
            $table->primary(['coupon_id', 'product_id']);

            $table->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('coupon_category', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('category_id');
            $table->primary(['coupon_id', 'category_id']);

            $table->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_category');
        Schema::dropIfExists('coupon_product');

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['free_product_id']);
            $table->dropColumn(['description', 'scope', 'free_product_id', 'max_discount']);
        });
    }
};
