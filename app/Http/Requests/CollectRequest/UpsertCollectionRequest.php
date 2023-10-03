<?php

namespace App\Http\Requests\CollectRequest;

use App\Rules\CheckCollectionCount;
use App\Rules\CheckCollectionNameUnique;
use Illuminate\Foundation\Http\FormRequest;

class UpsertCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => [
                "required",
                new CheckCollectionNameUnique,
                new CheckCollectionNameUnique,
                new CheckCollectionCount,
            ],
        ];
    }
}
