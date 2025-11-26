<?php

declare(strict_types=1);

// app/Http/Requests/UploadMediaRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
            'collection' => ['sometimes', 'string', 'max:50'],
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please select an image to upload',
            'image.image' => 'The file must be an image',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, GIF, or WebP file',
            'image.max' => 'The image must not be larger than 5MB',
        ];
    }
}
