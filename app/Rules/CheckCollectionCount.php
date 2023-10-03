<?php

namespace App\Rules;

use App\Models\Collection;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Database\Eloquent\Builder;

class CheckCollectionCount implements InvokableRule
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
        $count = Collection::query()
            ->where("user_id", auth()->id())
            ->when(request('collection'), function (Builder $q, Collection $old) {
                $q->whereNot('id', $old->id);
            })
            ->count();

        if ($count >= 5) {
            $fail(__('messages.collection_must_not_be_greater_than_five'));
        }
    }
}
