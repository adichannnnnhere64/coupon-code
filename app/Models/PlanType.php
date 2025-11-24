<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PlanType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
}
