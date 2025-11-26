<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\CouponUnavailableException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Coupon extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        throw_if($this->stock_quantity <= 0, CouponUnavailableException::class, 'Coupon out of stock');

        $this->decrement('stock_quantity');
    }

    public function incrementStock(int $quantity = 1): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(150)      // adjust width
            ->height(150)     // adjust height
            ->sharpen(10)
            ->nonQueued();    // optional, generates immediately instead of queue
    }

    // Helper methods
    protected function getImageUrlsAttribute(): array
    {
        return $this->getMedia('images')->map(fn($media): array => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumbnail' => $media->getUrl('thumbnail'),
            'name' => $media->name,
        ])->all();
    }

    protected function getPrimaryImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images');
    }
}
