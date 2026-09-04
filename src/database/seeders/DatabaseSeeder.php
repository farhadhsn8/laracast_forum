<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seedRoles();
        $this->seedPermissions();
    }

    /**
     * Seed the application roles.
     *
     * @return void
     */
    private function seedRoles(): void
    {
        foreach (config('permission.default_roles') as $role) {
            if (! Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }
    }

    /**
     * Seed the application permissions.
     *
     * @return void
     */
    private function seedPermissions(): void
    {
        foreach (config('permission.default_permissions') as $permission) {
            if (! Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
