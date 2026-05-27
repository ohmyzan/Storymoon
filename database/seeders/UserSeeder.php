<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles — semua snake_case konsisten
        $roles = ['super_admin', 'admin', 'editor', 'moderator', 'finance', 'author', 'reader'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Generate Permission Shield untuk Staff
        $panels = ['admin', 'editor', 'finance', 'moderator', 'super-admin'];

        foreach ($panels as $panel) {
            Artisan::call('shield:generate', [
                '--all' => true,
                '--ignore-existing-policies' => true,
                '--panel' => $panel,
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('permission:cache-reset');

        // 3. Data User Utama — role name konsisten snake_case
        $users = [
            ['name' => 'Pak Super',     'email' => 'super@storymoon.com',     'role' => 'super_admin'],
            ['name' => 'Pak Admin',     'email' => 'admin@storymoon.com',     'role' => 'admin'],
            ['name' => 'Mbak Editor',   'email' => 'editor@storymoon.com',    'role' => 'editor'],
            ['name' => 'Mas Mod',       'email' => 'moderator@storymoon.com', 'role' => 'moderator'],
            ['name' => 'Bu Finance',    'email' => 'finance@storymoon.com',   'role' => 'finance'],
            ['name' => 'Heavenly Dao',  'email' => 'author@storymoon.com',    'role' => 'author'],
            ['name' => 'Reader Setia',  'email' => 'reader@storymoon.com',    'role' => 'reader'],
        ];

        foreach ($users as $userData) {
            $attributes = [
                'name'               => $userData['name'],
                'password'           => 'password',
                'email_verified_at'  => now(),
            ];

            if ($userData['role'] === 'editor') {
                $attributes['editor_verified_at'] = now();
            }

            if ($userData['role'] === 'author') {
                $attributes['author_verified_at'] = now();
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $attributes
            );

            $user->syncRoles([$userData['role']]);
        }

        // ✅ Beri semua permission ke role super_admin
        $superAdminRole = Role::findByName('super_admin');

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::all());
        }

        // ✅ Reset cache permission di akhir
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('permission:cache-reset');
    }
}
