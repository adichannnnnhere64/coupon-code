<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class CouponTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'transaction_id',
        'amount',
        /* 'delivery_methods', */
        'status',
        'coupon_delivered_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        /* 'delivery_methods' => 'array', */
        'coupon_delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'success',
            'coupon_delivered_at' => now(),
        ]);
    }
}
