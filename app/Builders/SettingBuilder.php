<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class SettingBuilder extends Builder
{
    public function whereRecommended()
    {
        $this->where('name', 'is_recommended');

        return $this;
    }

    public function whereManualLogin()
    {
        $this->where('name', 'manual_login');

        return $this;
    }

    public function whereFacebookLogin()
    {
        $this->where('name', 'facebook_login');

        return $this;
    }

    public function whereDefaultCity()
    {
        $this->where('name', 'default_city');

        return $this;
    }
}
