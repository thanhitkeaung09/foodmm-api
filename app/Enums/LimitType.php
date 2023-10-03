<?php

namespace App\Enums;

enum LimitType: int
{
    case PAGINATE = 20;

    case LIMIT = 4;

    case WEBSITE = 5;
}
