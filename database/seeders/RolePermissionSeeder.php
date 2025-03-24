<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {

        // Create Roles and Assign Permissions
        $SuperAdminRole = Role::firstOrCreate(['name' => 'super admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $SuperAdminRole->givePermissionTo(Permission::all());

        $superAdmin = User::updateOrCreate([
            'name' => 'admin',
            'phone' => '01000000000',
            'email_verified_at' => now(),
            'password' => Hash::make('adminadmin')
        ]);
           // Assign Role
           $superAdmin->assignRole('super admin');

    }
}
