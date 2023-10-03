<?php

namespace Database\Seeders;

use App\Models\RatingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RatingType::query()->insert([
            [
                'name' => 'Food Taste',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer Service',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
