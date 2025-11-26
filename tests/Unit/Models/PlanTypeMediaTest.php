<?php

declare(strict_types=1);

// tests/Unit/Models/PlanTypeMediaTest.php
use App\Models\PlanType;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

it('can have icon', function (): void {
    $planType = PlanType::factory()->create();
    $image = $this->createTestImage();

    $planType->addMedia($image)->toMediaCollection('icon');

    $this->assertMediaAttached($planType, 'icon');
    expect($planType->hasIcon())->toBeTrue();
});

it('icon url accessor works', function (): void {
    $planType = PlanType::factory()->create();
    $image = $this->createTestImage();

    $planType->addMedia($image)->toMediaCollection('icon');

    expect($planType->icon_url)->not->toBeNull();
    expect($planType->icon_url)->toBeString();
});

it('icon collection accepts svg', function (): void {
    $planType = PlanType::factory()->withImage(collection: 'icon')->create();

    $collections = $planType->mediaCollections;
    expect($collections['icon']->acceptsMimeTypes)->toContain('image/svg+xml');
});

it('icon collection is single file', function (): void {
    $planType = PlanType::factory()->withImage(collection: 'icon')->create();

    $collections = $planType->mediaCollections;
    expect($collections['icon']->singleFile)->toBeTrue();
});
