<?php

declare(strict_types=1);

// app/Http/Requests/UploadMediaFromUrlRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UploadMediaFromUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url'],
            'collection' => ['sometimes', 'string', 'max:50'],
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
