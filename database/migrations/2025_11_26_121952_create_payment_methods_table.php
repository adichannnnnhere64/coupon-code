<?php

declare(strict_types=1);

// database/migrations/xxxx_xx_xx_xxxxxx_create_payment_methods_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table): void {
            $table->id();
            $table->string('name'); // Stripe, PayPal, Wallet, etc.
            $table->string('code')->unique(); // stripe, paypal, wallet
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // Gateway-specific configuration
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot table for coupon payment methods
        Schema::create('coupon_payment_method', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['coupon_id', 'payment_method_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_payment_method');
        Schema::dropIfExists('payment_methods');
    }
};
