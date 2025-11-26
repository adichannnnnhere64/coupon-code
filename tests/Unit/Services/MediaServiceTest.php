<?php

declare(strict_types=1);

// tests/Unit/Services/MediaServiceTest.php
use App\Models\Operator;
use App\Services\MediaService;
use Tests\Helpers\InteractsWithMedia;

uses(InteractsWithMedia::class);

beforeEach(function (): void {
    $this->mediaService = app(MediaService::class);
});

it('can attach image to model', function (): void {
    $operator = Operator::factory()->create();
    $image = $this->createTestImage();

    $media = $this->mediaService->attachImage($operator, $image, 'logo');

    $this->assertMediaAttached($operator, 'logo');
    expect($media->model_id)->toBe($operator->id);
    expect($media->model_type)->toBe(Operator::class);
    expect($media->collection_name)->toBe('logo');
});

it('can attach image with custom name and properties', function (): void {
    $operator = Operator::factory()->create();
    $image = $this->createTestImage();
    $customProperties = ['type' => 'logo', 'uploaded_by' => 'test'];

    $media = $this->mediaService->attachImage(
        $operator,
        $image,
        'logo',
        'custom-logo-name',
        $customProperties
    );

    expect($media->name)->toBe('custom-logo-name');
    expect($media->custom_properties)->toBe($customProperties);
});

it('can attach image from url', function (): void {
    $operator = Operator::factory()->create();
    $imageUrl = $this->createTestImageFromUrl();

    $media = $this->mediaService->attachImageFromUrl($operator, $imageUrl, 'logo');

    $this->assertMediaAttached($operator, 'logo');
    expect($media->getUrl())->not->toBeNull();
});

it('can get image url', function (): void {
    $operator = Operator::factory()->create();
    $image = $this->createTestImage();

    $this->mediaService->attachImage($operator, $image, 'logo');
    $url = $this->mediaService->getImageUrl($operator, 'logo');

    expect($url)->not->toBeNull();
    expect($url)->toBeString();
});

it('returns null for nonexistent image', function (): void {
    $operator = Operator::factory()->create();

    $url = $this->mediaService->getImageUrl($operator, 'logo');

    expect($url)->toBeNull();
});

it('can get all images from collection', function (): void {
    $operator = Operator::factory()->create();

    $this->mediaService->attachImage($operator, $this->createTestImage('image1.jpg'), 'logos');
    $this->mediaService->attachImage($operator, $this->createTestImage('image2.jpg'), 'logos');

    $images = $this->mediaService->getImages($operator, 'logos');

    expect($images)->toHaveCount(2);
});

it('can clear media collection', function (): void {
    $operator = Operator::factory()->create();

    $this->mediaService->attachImage($operator, $this->createTestImage(), 'logo');
    $this->assertMediaAttached($operator, 'logo');

    $this->mediaService->clearCollection($operator, 'logo');
    $this->assertMediaNotAttached($operator, 'logo');
});

it('can delete specific image', function (): void {
    $operator = Operator::factory()->create();

    $media = $this->mediaService->attachImage($operator, $this->createTestImage(), 'logo');

    $this->assertMediaAttached($operator, 'logo');

    $this->mediaService->deleteImage($operator, $media->id);
    $this->assertMediaNotAttached($operator->fresh(), 'logo');
});
