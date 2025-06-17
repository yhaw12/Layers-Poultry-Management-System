<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'view-sales']);
        Permission::create(['name' => 'create-sales']);

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['view-sales', 'create-sales']);

        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo('view-sales');

        // Assign role to a user (e.g., user ID 1)
        $user = \App\Models\User::find(1);
        $user->assignRole('admin');
    }
}