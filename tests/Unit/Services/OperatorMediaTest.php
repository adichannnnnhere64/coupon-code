<?php

declare(strict_types=1);

// tests/Unit/Models/OperatorMediaTest.php
use App\Models\Operator;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

it('can have logo', function (): void {
    $operator = Operator::factory()->create();
    $image = $this->createTestImage();

    $operator->addMedia($image)->toMediaCollection('logo');

    $this->assertMediaAttached($operator, 'logo');
    expect($operator->hasLogo())->toBeTrue();
});

it('logo url accessor works', function (): void {
    $operator = Operator::factory()->create();
    $image = $this->createTestImage();

    $operator->addMedia($image)->toMediaCollection('logo');

    expect($operator->logo_url)->not->toBeNull();
    expect($operator->logo_url)->toBeString();
});

it('logo url falls back to legacy logo url', function (): void {
    $operator = Operator::factory()->create([
        'logo_url' => 'https://example.com/old-logo.png',
    ]);

    /* dd($operator->logo_url); */

    expect($operator->logo_url)->toBe('https://example.com/old-logo.png');
});

/* it('logo thumbnail url works', function () { */
/*     $operator = Operator::factory()->create(); */
/*     $image = $this->createTestImage(); */
/**/
/*     $media = $operator->addMedia($image)->toMediaCollection('logo'); */
/**/
/*     expect($operator->logo_thumbnail_url)->not->toBeNull(); */
/*     $this->assertMediaHasConversion($media, 'thumbnail'); */
/* }); */

it('media collection accepts correct mime types', function (): void {
    $operator = Operator::factory()->withLogo()->create();

    $collections = $operator->mediaCollections;
    $logoCollection = collect($collections)->firstWhere('name', 'logo');

    expect($logoCollection)->not->toBeNull();
    expect($logoCollection->acceptsMimeTypes)->toContain('image/jpeg');
    expect($logoCollection->acceptsMimeTypes)->toContain('image/png');
    expect($logoCollection->acceptsMimeTypes)->toContain('image/webp');
});

it('logo collection is single file', function (): void {
    $operator = Operator::factory()->withLogo()->create();

    $collections = $operator->mediaCollections;
    $logoCollection = collect($collections)->firstWhere('name', 'logo');

    expect($logoCollection)->not->toBeNull();
    expect($logoCollection->singleFile)->toBeTrue();
});
