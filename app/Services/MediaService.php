<?php

declare(strict_types=1);

// app/Services/MediaService.php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MediaService
{
    public function attachImage(
        Model $model,
        UploadedFile $file,
        string $collection = 'default',
        ?string $name = null,
        array $customProperties = []
    ): Media {
        return $model
            ->addMedia($file)
            ->usingName($name ?? $file->getClientOriginalName())
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);
    }

    public function attachImageFromUrl(
        Model $model,
        string $url,
        string $collection = 'default',
        ?string $name = null,
        array $customProperties = []
    ): Media {
        return $model
            ->addMediaFromUrl($url)
            ->usingName($name ?? basename($url))
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);
    }

    public function attachImageFromBase64(
        Model $model,
        string $base64Data,
        string $filename,
        string $collection = 'default',
        array $customProperties = []
    ): Media {
        // Remove data:image/...;base64, prefix if present
        $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $base64Data);

        return $model
            ->addMediaFromBase64($base64Data)
            ->usingFileName($filename)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);
    }

    public function getImageUrl(Model $model, string $collection = 'default', string $conversion = ''): ?string
    {
        $media = $model->getFirstMedia($collection);

        return $media ? $media->getUrl($conversion) : null;
    }

    public function getImages(Model $model, string $collection = 'default'): Collection
    {
        return $model->getMedia($collection);
    }

    public function clearCollection(Model $model, string $collection = 'default'): void
    {
        $model->clearMediaCollection($collection);
    }

    public function deleteImage(Model $model, int $mediaId): void
    {
        $model->deleteMedia($mediaId);
    }

    public function updateImageOrder(Model $model, array $mediaIds, string $collection = 'default'): void
    {
        Media::setNewOrder($mediaIds);
    }

    public function getImageUrlsWithConversions(Model $model, string $collection = 'default'): array
    {
        $media = $model->getFirstMedia($collection);

        if (! $media) {
            return [];
        }

        return [
            'original' => $media->getUrl(),
            'thumbnail' => $media->getUrl('thumbnail'),
            'medium' => $media->getUrl('medium'),
            'large' => $media->getUrl('large'),
        ];
    }
}
