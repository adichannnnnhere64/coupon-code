<?php

declare(strict_types=1);

// tests/Helpers/InteractsWithMedia.php

namespace Tests\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait InteractsWithMedia
{
    protected function createTestImage(string $filename = 'test-image.jpg'): UploadedFile
    {
        Storage::fake('public');

        return UploadedFile::fake()->image($filename, 400, 300);
    }

    protected function createTestImageFromUrl(): string
    {
        return 'https://placehold.co/600x400/png';
    }

    protected function assertMediaAttached($model, string $collection = 'default'): void
    {
        expect($model->getMedia($collection))->toHaveCount(1);
    }

    protected function assertMediaNotAttached($model, string $collection = 'default'): void
    {
        expect($model->getMedia($collection))->toHaveCount(0);
    }

    protected function assertMediaHasConversion($media, string $conversionName): void
    {
        expect($media->hasGeneratedConversion($conversionName))->toBeTrue();
    }
}
