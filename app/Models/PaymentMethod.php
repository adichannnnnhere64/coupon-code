<?php

declare(strict_types=1);

// app/Models/PaymentMethod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'display_name',
        'description',
        'is_active',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_payment_method')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function isWallet(): bool
    {
        return $this->code === 'wallet';
    }

    public function isGateway(): bool
    {
        return in_array($this->code, ['stripe', 'paypal']);
    }

    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }
}
