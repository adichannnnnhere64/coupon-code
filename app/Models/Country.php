<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'currency', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    public function coupons()
    {
        return $this->hasManyThrough(Coupon::class, Operator::class);
    }
}
