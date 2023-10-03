<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
            'food_taste_rating' => ['required'],
            'customer_service_rating' => ['required'],
            'review' => ['required', 'string'],
            'images' => ['nullable', 'array'],
            // 'images.*' => ['image'],
        ];
    }
}
