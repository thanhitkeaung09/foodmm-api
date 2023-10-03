<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class FoodCategoryBuilder extends Builder
{
    public function whereCuisine()
    {
        $this->where('slug', 'cuisines');

        return $this;
    }
}
