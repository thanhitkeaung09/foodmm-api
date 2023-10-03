<?php

namespace App\Rules;

use App\Models\Collection;
use Illuminate\Contracts\Validation\InvokableRule;

class CheckCollectionName implements InvokableRule
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
        $exists = Collection::query()->where('user_id', auth()->id())->exists();

        if ($exists) {
            $fail(__('messages.collection_name_already_exists'));
        }
    }
}
