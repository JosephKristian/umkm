<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCreate = User::create([
            'name'      => 'super admin',
            'email'     => 'sa@gmail.com',
            'password'  => bcrypt('password')
        ]);



        // $brand = Brand::find(3);
        // $brand->users_id = $userCreate->id;
        // $brand->save();
        // //assign permission to role
        $role = Role::find(1);
        $permissions = Permission::all();

        $role->syncPermissions($permissions);

        //assign role with permission to user
        $user = User::find(1);
        $user->assignRole($role->name);
    }
}
