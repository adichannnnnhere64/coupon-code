<?php

declare(strict_types=1);

// tests/Unit/Models/CouponMediaTest.php
use App\Models\Coupon;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

it('can have multiple images', function (): void {
    $coupon = Coupon::factory()->create();

    $coupon->addMedia($this->createTestImage('image1.jpg'))->toMediaCollection('images');
    $coupon->addMedia($this->createTestImage('image2.jpg'))->toMediaCollection('images');

    $coupon->fresh();

    expect($coupon->getMedia('images'))->toHaveCount(2);
});

it('image urls accessor works', function (): void {
    $coupon = Coupon::factory()->create();

    $media1 = $coupon->addMedia($this->createTestImage('image1.jpg'))->toMediaCollection('images');
    $media2 = $coupon->addMedia($this->createTestImage('image2.jpg'))->toMediaCollection('images');

    $imageUrls = $coupon->fresh()->image_urls;

    expect($imageUrls)->toHaveCount(2);
    expect($imageUrls[0]['id'])->toBe($media1->id);
    expect($imageUrls[0]['url'])->not->toBeNull();
    expect($imageUrls[0]['thumbnail'])->not->toBeNull();
});

it('primary image url accessor works', function (): void {
    $coupon = Coupon::factory()->create();

    $coupon->addMedia($this->createTestImage())->toMediaCollection('images');

    expect($coupon->primary_image_url)->not->toBeNull();
    expect($coupon->primary_image_url)->toBeString();
});

it('primary image url returns null when no images', function (): void {
    $coupon = Coupon::factory()->withImage()->create();

    expect($coupon->fresh()->primary_image_url)->toBeEmpty();
});

it('images collection generates responsive images', function (): void {
    $coupon = Coupon::factory()->create();

    $media = $coupon->addMedia($this->createTestImage())->toMediaCollection('images');

    expect($media->fresh()->responsive_images)->not->toBeEmpty();
});
