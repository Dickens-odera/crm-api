<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //DB::table('roles')->truncate();
        $roles = array(
          array(
              'name' => 'admin',
              'guard_name' => 'api'
          ),
          array(
              'name' => 'user',
              'guard_name' => 'api'
          ),
        );
        Role::insert($roles);
    }
}
