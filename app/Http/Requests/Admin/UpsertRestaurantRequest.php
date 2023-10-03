<?php

namespace App\Http\Requests\Admin;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\State;
use App\Models\Township;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertRestaurantRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique(Restaurant::class)->ignore($this->restaurant)],
            'description' => ['required', 'string'],
            'phones' => ['nullable', 'string'],
            'opening_hours' => ['array'],
            'opening_hours.*' => ['string'],
            'category_id' => ['required', 'integer', Rule::exists(RestaurantCategory::class, 'id')],
            'township_id' => ['required', 'integer', Rule::exists(Township::class, 'id')],
            'address' => ['required', 'string'],
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'images' => ['sometimes', 'array'],
        ];
    }
}
