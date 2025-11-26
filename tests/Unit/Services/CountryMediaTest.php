<?php

declare(strict_types=1);

// tests/Unit/Models/CountryMediaTest.php
use App\Models\Country;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

it('can have flag', function (): void {
    $country = Country::factory()->create();
    $image = $this->createTestImage();

    $country->addMedia($image)->toMediaCollection('flag');

    $this->assertMediaAttached($country, 'flag');
    expect($country->hasFlag())->toBeTrue();
});

it('flag url accessor works', function (): void {
    $country = Country::factory()->create();
    $image = $this->createTestImage();

    $country->addMedia($image)->toMediaCollection('flag');

    expect($country->flag_url)->not->toBeNull();
    expect($country->flag_url)->toBeString();
});

it('flag collection accepts svg', function (): void {
    $country = Country::factory()->withImage(collection: 'flag')->create();

    $collections = $country->mediaCollections;
    expect($collections['flag']->acceptsMimeTypes)->toContain('image/svg+xml');
});

it('flag collection is single file', function (): void {
    $country = Country::factory()->withImage(collection: 'flag')->create();

    $collections = $country->mediaCollections;

    expect($collections['flag']->singleFile)->toBeTrue();
});
