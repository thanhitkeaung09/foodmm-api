<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = ['view', 'create', 'edit', 'delete'];

        $resources = [
            'admins' => Arr::except($actions, [2]),
            'roles' => $actions,
            'users' => Arr::except($actions, [1]),
            'foods' => $actions,
            'foods-types' => $actions,
            'foods-categories' => $actions,
            'restaurants' => $actions,
            'restaurants-categories' => $actions,
            'shops' => $actions,
            'shops-categories' => $actions,
            'states' => $actions,
            'cities' => $actions,
            'townships' => $actions,
            'plans' => $actions,
            'blogs' => $actions,
            'promotions' => $actions,
            'flash-screens' => $actions,
        ];

        $data = collect($resources)->map(function ($actions, $resource) {
            return collect($actions)->map(fn ($action) => ([
                'name' => "{$action}-{$resource}",
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]))->toArray();
        });

        $data->push([
            [
                'name' => 'view-cuisines',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'assign-roles',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'edit-admins-profile',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'edit-admins-password',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        Permission::query()->insert($data->flatten(1)->toArray());

        Role::findById(2, 'admin')->givePermissionTo(range(8, 67));
    }
}
