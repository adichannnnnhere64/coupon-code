<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_type_id')->constrained()->onDelete('cascade');
            $table->decimal('denomination', 8, 2);
            $table->decimal('selling_price', 8, 2);
            $table->string('coupon_code')->unique();
            $table->string('serial_number')->unique();
            $table->integer('validity_days')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator_id', 'plan_type_id', 'is_active']);
            $table->index(['stock_quantity', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
