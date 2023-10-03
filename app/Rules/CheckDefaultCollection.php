<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class CheckDefaultCollection implements InvokableRule
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
        $ids = request('data');

        $exists = auth()->user()->collections()
            ->whereIn('id', $ids)
            ->where('name', \config('plan.default_collection_name'))
            ->exists();

        if ($exists) {
            $fail(__('messages.collection_default'));
        }
    }
}
