<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class PlanType extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'description'];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }

    // Helper methods
    protected function getIconUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('icon');
    }

    public function hasIcon(): bool
    {
        return $this->hasMedia('icon');
    }
}
