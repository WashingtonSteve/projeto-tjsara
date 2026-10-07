<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Tag\Entities\Tag;
use Illuminate\Foundation\Http\FormRequest;

final class AttachTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.Tag::NAME_MAX_LENGTH],
        ];
    }
}
