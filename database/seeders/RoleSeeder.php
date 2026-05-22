<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Super Admin',
            'Admin',
            'Editor',
            'Moderator',
            'Finance',
            'Author',
            'Reader'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
