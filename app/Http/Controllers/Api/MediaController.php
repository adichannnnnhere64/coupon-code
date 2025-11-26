<?php

declare(strict_types=1);

// app/Http/Controllers/Api/MediaController.php

namespace App\Http\Controllers\Api;

use App\Models\Operator;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\PlanType;
use App\Exceptions\CustomException;
use App\Http\Requests\UploadMediaFromUrlRequest;
use App\Http\Requests\UploadMediaRequest;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class MediaController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function upload(UploadMediaRequest $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->resolveModel($modelType, $modelId);

        $media = $this->mediaService->attachImage(
            model: $model,
            file: $request->file('image'),
            collection: $request->input('collection', 'default'),
            name: $request->input('name'),
            customProperties: $request->input('custom_properties', [])
        );

        return response()->json([
            'message' => 'Image uploaded successfully',
            'media' => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'collection' => $media->collection_name,
            ],
        ], 201);
    }

    public function uploadFromUrl(UploadMediaFromUrlRequest $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->resolveModel($modelType, $modelId);

        $media = $this->mediaService->attachImageFromUrl(
            model: $model,
            url: $request->input('url'),
            collection: $request->input('collection', 'default'),
            name: $request->input('name'),
            customProperties: $request->input('custom_properties', [])
        );

        return response()->json([
            'message' => 'Image uploaded successfully from URL',
            'media' => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'collection' => $media->collection_name,
            ],
        ], 201);
    }

    public function getImages(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->resolveModel($modelType, $modelId);
        $collection = $request->input('collection', 'default');

        $images = $this->mediaService->getImages($model, $collection)
            ->map(fn($media): array => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumbnail'),
                'name' => $media->name,
                'collection' => $media->collection_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'custom_properties' => $media->custom_properties,
            ]);

        return response()->json([
            'images' => $images,
        ]);
    }

    public function deleteImage(string $modelType, int $modelId, int $mediaId): JsonResponse
    {
        $model = $this->resolveModel($modelType, $modelId);

        $this->mediaService->deleteImage($model, $mediaId);

        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }

    public function clearCollection(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $model = $this->resolveModel($modelType, $modelId);
        $collection = $request->input('collection', 'default');

        $this->mediaService->clearCollection($model, $collection);

        return response()->json([
            'message' => 'Collection cleared successfully',
        ]);
    }

    private function resolveModel(string $modelType, int $modelId)
    {

        $modelClass = match ($modelType) {
            'operator' => Operator::class,
            'country' => Country::class,
            'coupon' => Coupon::class,
            'plan-type' => PlanType::class,
            default => 'invalid-type'
        };

        throw_unless($modelClass !== 'invalid-type', CustomException::invalidImage());

        // Add authorization check if needed
        // $this->authorize('update', $model);

        return $modelClass::findOrFail($modelId);
    }
}
