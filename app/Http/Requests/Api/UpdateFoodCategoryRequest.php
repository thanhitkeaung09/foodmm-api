<?php

namespace App\Http\Requests\Api;

use App\Rules\CheckFoodCategoriesExists;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodCategoryRequest extends FormRequest
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
            'preferr_ids' => ['array', new CheckFoodCategoriesExists,],
        ];
    }
}
