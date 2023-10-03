<?php

declare(strict_types=1);

use App\Models\Restaurant;
use App\Models\Shop;

return [
    'types' => [
        Shop::class => 'shop',
        Restaurant::class => 'restaurant',
    ]
];
