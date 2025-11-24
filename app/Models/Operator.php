<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Operator extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name', 'code', 'logo_url', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
}
