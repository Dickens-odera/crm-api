<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //DB::table('users')->truncate();
        $admin = User::firstWhere('email', 'admin@agilemonkeys.com');
        if(!$admin){
            $adminUser = User::create([
                'name'  => 'Admin',
                'email' => 'admin@agilemonkeys.com',
                'password' => Hash::make('admin123')
            ]);
            $roleAdmin = Role::findOrCreate('admin','api');
            $roleUser  = Role::findOrCreate('user','api');
            $adminUser->assignRole([$roleAdmin, $roleUser]);
        }
    }
}
