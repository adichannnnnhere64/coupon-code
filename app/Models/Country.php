<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Country extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('flag')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }

    // Helper methods
    protected function getFlagUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('flag');
    }

    public function hasFlag(): bool
    {
        return $this->hasMedia('flag');
    }
}
