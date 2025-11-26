<?php

declare(strict_types=1);

// tests/Unit/Models/AllModelsMediaTest.php
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PlanType;
use Spatie\MediaLibrary\HasMedia;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

it('can attach images to all models', function (): void {
    $operator = Operator::factory()->create();
    $country = Country::factory()->create();
    $coupon = Coupon::factory()->create();
    $planType = PlanType::factory()->create();

    $operator->addMedia($this->createTestImage())->toMediaCollection('logo');
    $country->addMedia($this->createTestImage())->toMediaCollection('flag');
    $coupon->addMedia($this->createTestImage())->toMediaCollection('images');
    $planType->addMedia($this->createTestImage())->toMediaCollection('icon');

    expect($operator->hasLogo())->toBeTrue();
    expect($country->hasFlag())->toBeTrue();
    expect($coupon->getMedia('images'))->toHaveCount(1);
    expect($planType->hasIcon())->toBeTrue();
});

it('all models implement HasMedia interface', function (): void {
    $operator = new Operator();
    $country = new Country();
    $coupon = new Coupon();
    $planType = new PlanType();

    expect($operator)->toBeInstanceOf(HasMedia::class);
    expect($country)->toBeInstanceOf(HasMedia::class);
    expect($coupon)->toBeInstanceOf(HasMedia::class);
    expect($planType)->toBeInstanceOf(HasMedia::class);
});

it('all models have media collections defined', function (): void {
    $operator = Operator::factory()->withLogo()->create();
    $country = Country::factory()->withImage()->create();
    $coupon = Coupon::factory()->withImage()->create();
    $planType = PlanType::factory()->withImage()->create();

    expect($operator->mediaCollections)->not->toBeEmpty();
    expect($country->mediaCollections)->not->toBeEmpty();
    expect($coupon->mediaCollections)->not->toBeEmpty();
    expect($planType->mediaCollections)->not->toBeEmpty();
});
