<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
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
                'name' => 'Super Admin',
                'email' => 'foodmm.superadmin@gmail.com',
                'password' => Hash::make('superadmin@1359#'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'email' => 'foodmm.admin@gmail.com',
                'password' => Hash::make('admin@2468#'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Admin::query()->insert($data);
    }
}
