<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Operator extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->registerMediaConversions(function (Media $media): void {
                $this
                    ->addMediaConversion('thumbnail')
                    ->quality(80);

                $this
                    ->addMediaConversion('medium')
                    ->quality(85);

                $this
                    ->addMediaConversion('large')
                    ->quality(90);
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('optimized')
            ->quality(85);
    }

    // Helper methods
    protected function getLogoUrlAttribute(): ?string
    {
        // get media URL if it exists
        $mediaUrl = $this->getFirstMediaUrl('logo', 'medium');

        // fallback to the database column directly to avoid calling the accessor
        return $mediaUrl ?: $this->attributes['logo_url'] ?? null;
    }

    protected function getLogoThumbnailUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo', 'thumbnail');
    }

    public function hasLogo(): bool
    {
        return $this->hasMedia('logo');
    }
}
