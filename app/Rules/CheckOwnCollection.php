<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;

class CheckOwnCollection implements InvokableRule
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
        $user = User::query()->where('id', request('user_id'))->first();

        $exists = $user->collections()->where('id', $value)->exists();

        if (!$exists) {
            $fail(__('messages.own_collection'));
        }
    }
}
