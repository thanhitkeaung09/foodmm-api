<?php

namespace App\Rules;

use App\Models\FoodCategory;
use Illuminate\Contracts\Validation\InvokableRule;

class CheckFoodCategoriesExists implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $count = FoodCategory::query()->whereIn('id', $value)->count();

        if ($count !== count($value)) {
            $fail(__('messages.food_categories_exists'));
        }
    }
}
