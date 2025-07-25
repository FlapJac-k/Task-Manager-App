<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::where('name', 'manager')->exists()) {
            return;
        }

        $roles = ['manager', 'user'];
        $permissions = [
            'create tasks',
            'edit tasks',
            'view tasks',
            'delete tasks',
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::create(['name' => $roleName]);
        }

        $permissionModels = [];
        foreach ($permissions as $permissionName) {
            $permissionModels[$permissionName] = Permission::create(['name' => $permissionName]);
        }

        $roleModels['manager']->givePermissionTo([
            $permissionModels['create tasks'],
            $permissionModels['edit tasks'],
            $permissionModels['view tasks'],
            $permissionModels['delete tasks'],
        ]);

        $roleModels['user']->givePermissionTo([
            $permissionModels['edit tasks'],
            $permissionModels['view tasks'],
        ]);

        $users = [
            'manager@example.com' => 'manager',
            'user@example.com' => 'user',
            'user2@example.com' => 'user',
        ];

        foreach ($users as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole($roleName);
            } else {
                $this->command->warn("User with email {$email} not found. Role not assigned.");
            }
        }

    }
}
