<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class CheckOwnCollections implements InvokableRule
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

        $ownCounts = auth()->user()->collections()->whereIn('id', $ids)->count();

        if ($ownCounts !== count($ids)) {
            $fail(__('messages.without_permission'));
        }
    }
}
