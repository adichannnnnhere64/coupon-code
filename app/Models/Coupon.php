<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\CouponUnavailableException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'plan_type_id',
        'denomination',
        'selling_price',
        'coupon_code',
        'serial_number',
        'validity_days',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'denomination' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function planType()
    {
        return $this->belongsTo(PlanType::class);
    }

    public function transactions()
    {
        return $this->hasMany(CouponTransaction::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function decrementStock(): void
    {
        throw_if($this->stock_quantity <= 0, new CouponUnavailableException('Coupon out of stock'));

        $this->decrement('stock_quantity');
    }

    public function incrementStock(int $quantity = 1): void
    {
        $this->increment('stock_quantity', $quantity);
    }
}
