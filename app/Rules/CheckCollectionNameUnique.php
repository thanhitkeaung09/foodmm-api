<?php

namespace App\Rules;

use App\Models\Collection;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Database\Eloquent\Builder;

class CheckCollectionNameUnique implements InvokableRule
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
        $exists = Collection::query()
            ->where("user_id", auth()->id())
            ->when(request('collection'), function (Builder $q, Collection $old) {
                $q->whereNot('id', $old->id);
            })
            ->where('name', $value)
            ->exists();

        if ($exists) {
            $fail(__('messages.collection_name_already_exists'));
        }
    }
}
