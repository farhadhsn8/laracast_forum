<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_in_database = Role::where('name', config('permission.default_roles')[0]);

        if($role_in_database->count() < 1)
        {
            foreach (config('permission.default_roles') as $role)
            {
                Role::create([
                    'name' =>$role
                ]);
            }
        }

        $permission_in_database = Permission::where('name', config('permission.default_permissions')[0]);

        if($permission_in_database->count() < 1)
        {
            foreach (config('permission.default_permissions') as $permission)
            {
                Permission::create([
                    'name' =>$permission
                ]);
            }
        }

        

    }
}
