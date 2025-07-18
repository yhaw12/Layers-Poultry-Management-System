<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage finances']);
        Permission::create(['name' => 'manage tasks']);

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view dashboard', 'manage users', 'manage finances', 'manage tasks']);

        $accountantRole = Role::create(['name' => 'accountant']);
        $accountantRole->givePermissionTo(['view dashboard', 'manage finances']);

        $labourerRole = Role::create(['name' => 'labourer']);
        $labourerRole->givePermissionTo(['view dashboard', 'manage tasks']);
    }
}