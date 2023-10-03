<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'default_city',
                'value' => 'Yangon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'is_recommended',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'manual_login',
                'value' => 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Setting::query()->insert($data);
    }
}
