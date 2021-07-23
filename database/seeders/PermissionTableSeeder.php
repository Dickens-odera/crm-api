<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissionsByRole = [
            'admin' => [
                'create user','view user','update user','delete user','list users',
                //'add customer', 'view customer', 'update customer','delete customer',
                'create role','update role','list roles','view role','delete role',
                //'view profile','update profile',
                'assign roles','change admin status','list user roles'
            ],
            'user' => [
                'add customer', 'view customer', 'update customer','delete customer',
                'view profile','update profile'
            ],
        ];

        $insertPermissions = function ($role) use ($permissionsByRole) {
            return collect($permissionsByRole[$role])
                ->map(function ($name){
                    return DB::table('permissions')->insertGetId(['name' => $name, 'guard_name' => 'api']);
                })
                ->toArray();
        };

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin'),
            'user' => $insertPermissions('user'),
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(function ($id) use ($role) {
                        return [
                            'role_id' => $role->id,
                            'permission_id' => $id
                        ];
                    })->toArray()
                );
        }
    }
}
