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
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('type')->default('image')->after('sort_order');
            $table->string('video_url')->nullable()->after('type');
            $table->string('video_thumbnail')->nullable()->after('video_url');
            $table->string('orientation')->nullable()->after('video_thumbnail');
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['type', 'video_url', 'video_thumbnail', 'orientation']);
        });
    }
};
