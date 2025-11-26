<?php

declare(strict_types=1);

// tests/Feature/MediaApiTest.php
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PlanType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

it('can upload image to operator', function (): void {
    $operator = Operator::factory()->create();
    $this->signIn();

    $response = $this->postJson("/api/media/operator/{$operator->id}/upload", [
        'image' => UploadedFile::fake()->image('operator-logo.jpg'),
        'collection' => 'logo',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'media' => ['id', 'url', 'name', 'collection'],
        ]);

    $this->assertDatabaseHas('media', [
        'model_id' => $operator->id,
        'model_type' => Operator::class,
        'collection_name' => 'logo',
    ]);
});

it('can upload image to country', function (): void {
    $country = Country::factory()->create();
    $this->signIn();

    $response = $this->postJson("/api/media/country/{$country->id}/upload", [
        'image' => UploadedFile::fake()->image('country-flag.jpg'),
        'collection' => 'flag',
    ]);

    $response->assertStatus(201);
    expect($country->fresh()->hasFlag())->toBeTrue();
});

it('can upload image to coupon', function (): void {
    $coupon = Coupon::factory()->create();
    $this->signIn();

    $response = $this->postJson("/api/media/coupon/{$coupon->id}/upload", [
        'image' => UploadedFile::fake()->image('coupon-image.jpg'),
        'collection' => 'images',
    ]);

    $response->assertStatus(201);
    expect($coupon->fresh()->getMedia('images'))->toHaveCount(1);
});

it('can upload image to plan type', function (): void {
    $planType = PlanType::factory()->create();
    $this->signIn();

    $response = $this->postJson("/api/media/plan-type/{$planType->id}/upload", [
        'image' => UploadedFile::fake()->image('plan-icon.jpg'),
        'collection' => 'icon',
    ]);

    $response->assertStatus(201);
    expect($planType->fresh()->hasIcon())->toBeTrue();
});

it('can get images for model', function (): void {
    $operator = Operator::factory()->create();
    $operator->addMedia(UploadedFile::fake()->image('logo.jpg'))->toMediaCollection('logo');
    $this->signIn();

    $response = $this->getJson("/api/media/operator/{$operator->id}/images?collection=logo");

    $response->assertStatus(200)
        ->assertJsonStructure(['images' => [['id', 'url', 'thumbnail', 'name']]]);
});

it('can delete image from model', function (): void {
    $operator = Operator::factory()->create();
    $media = $operator->addMedia(UploadedFile::fake()->image('logo.jpg'))->toMediaCollection('logo');
    $this->signIn();

    $response = $this->deleteJson("/api/media/operator/{$operator->id}/images/{$media->id}");

    $response->assertStatus(200);
    expect($operator->fresh()->getMedia('logo'))->toHaveCount(0);
});

it('returns 404 for invalid model type', function (): void {
    $this->withExceptionHandling();
    $this->signIn();

    $response = $this->postJson('/api/media/invalid-type/1/upload', [
        'image' => UploadedFile::fake()->image('test.jpg'),
    ]);

    $response->assertStatus(404);
});
