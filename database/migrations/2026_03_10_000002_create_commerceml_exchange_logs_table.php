<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerceml_exchange_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // catalog | sale
            $table->string('mode', 20);
            $table->string('session_id')->nullable();
            $table->string('filename')->nullable();
            $table->string('status', 20)->default('success'); // success | error
            $table->text('message')->nullable();
            $table->unsignedInteger('products_created')->default(0);
            $table->unsignedInteger('products_updated')->default(0);
            $table->unsignedInteger('categories_created')->default(0);
            $table->unsignedInteger('categories_updated')->default(0);
            $table->unsignedInteger('orders_exported')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerceml_exchange_logs');
    }
};
