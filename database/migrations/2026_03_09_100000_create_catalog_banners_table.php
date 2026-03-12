<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('images');
            $table->unsignedInteger('position')->default(0);
            $table->string('display_mode')->default('full'); // full, double, single
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_banners');
    }
};
