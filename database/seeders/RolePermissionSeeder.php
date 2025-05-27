<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'worker']);
        Permission::create(['name' => 'view-logs']);
        Permission::create(['name' => 'manage-eggs']);
        // Add more permissions
        Role::findByName('admin')->givePermissionTo('view-logs', 'manage-eggs');
        Role::findByName('manager')->givePermissionTo('manage-eggs');
    }
}