<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class UserTestingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $roles = [
            'super_admin',
            'Admin',
            'Editor',
            'Moderator',
            'Finance',
            'Author',
            'Reader'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Generate Permission Shield untuk Staff (Selain Super Admin)
        $panels = ['admin', 'editor', 'finance', 'moderator', 'super-admin'];

        foreach ($panels as $panel) {
            Artisan::call('shield:generate', [
                '--all' => true,
                '--ignore-existing-policies' => true,
                '--panel' => $panel,
            ]);
        }

        // 3. Reset Permission Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('permission:cache-reset');

        // 4. Data User Testing
        // 🔥 PERBAIKAN: Hapus Hash::make() karena Laravel akan men-hash otomatis
        $password = 'password';

        $users = [
            [
                'name' => 'Pak Super',
                'email' => 'super@storymoon.com',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Pak Admin',
                'email' => 'admin@storymoon.com',
                'role' => 'Admin',
            ],
            [
                'name' => 'Mbak Editor',
                'email' => 'editor@storymoon.com',
                'role' => 'Editor',
            ],
            [
                'name' => 'Mas Mod',
                'email' => 'moderator@storymoon.com',
                'role' => 'Moderator',
            ],
            [
                'name' => 'Bu Finance',
                'email' => 'finance@storymoon.com',
                'role' => 'Finance',
            ],
            [
                'name' => 'Author Hebat',
                'email' => 'author@storymoon.com',
                'role' => 'Author',
            ],
            [
                'name' => 'Reader Setia',
                'email' => 'reader@storymoon.com',
                'role' => 'Reader',
            ],
        ];

        foreach ($users as $userData) {
            $attributes = [
                'name' => $userData['name'],
                'password' => $password, // Otomatis di-hash oleh model User
                'email_verified_at' => now(),
            ];

            if ($userData['role'] === 'Editor') {
                $attributes['editor_verified_at'] = now();
            }
            if ($userData['role'] === 'Author') {
                $attributes['author_verified_at'] = now();
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $attributes
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
