<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Use the app's default guard (fallback to 'web')
        $guardName = config('auth.defaults.guard', 'web') ?? 'web';

        // Full permission list (from your earlier file)
        $permissions = [
            // Bird Management
            'view_birds', 'create_birds', 'edit_birds', 'delete_birds', 'manage_birds',
            'view_eggs', 'create_eggs', 'edit_eggs', 'delete_eggs', 'manage_eggs',
            'view_mortalities', 'create_mortalities', 'edit_mortalities', 'delete_mortalities', 'manage_mortalities',
            'view_vaccination_logs', 'create_vaccination_logs', 'edit_vaccination_logs', 'delete_vaccination_logs', 'manage_vaccination_logs',

            // Resource Management
            'view_feed', 'create_feed', 'edit_feed', 'delete_feed', 'manage_feed',
            'view_medicine_logs', 'create_medicine_logs', 'edit_medicine_logs', 'delete_medicine_logs', 'manage_medicine_logs',
            'view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory', 'manage_inventory',
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers', 'manage_suppliers',

            // Sales and Customers
            'view_sales', 'create_sales', 'edit_sales', 'delete_sales', 'manage_sales',
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers', 'manage_customers',
            'view_orders', 'create_orders', 'edit_orders', 'delete_orders', 'manage_orders',
            'view_invoices', 'generate_invoices', 'manage_invoices',

            // Financial Management
            'view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses', 'manage_expenses',
            'view_income', 'create_income', 'edit_income', 'delete_income', 'manage_income',
            'view_payroll', 'create_payroll', 'edit_payroll', 'delete_payroll', 'manage_payroll', 'generate_payroll', 'manage_finances',

            // Health Management
            'view_health_checks', 'create_health_checks', 'edit_health_checks', 'delete_health_checks', 'manage_health_checks',
            'view_diseases', 'create_diseases', 'edit_diseases', 'delete_diseases', 'manage_diseases',

            // User and Role Management
            'view_users', 'create_users', 'edit_users', 'delete_users', 'manage_users',
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles', 'manage_roles',
            'assign_roles', 'toggle_permissions',

            // Reporting and Analytics
            'view_dashboard', 'export_dashboard',
            'view_reports', 'generate_reports', 'export_reports',
            'view_activity_logs', 'manage_activity_logs',
            'view_kpis',
        ];

        // Create permissions (explicit guard_name)
        DB::transaction(function () use ($permissions, $guardName) {
            foreach (array_chunk($permissions, 20) as $chunk) {
                foreach ($chunk as $permission) {
                    Permission::firstOrCreate(
                        ['name' => $permission, 'guard_name' => $guardName],
                        ['name' => $permission, 'guard_name' => $guardName]
                    );
                }
            }
        });

        // Create roles and assign permissions (explicit guard_name)
        DB::transaction(function () use ($permissions, $guardName) {
            // Admin (all permissions)
            $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guardName]);
            $admin->syncPermissions($permissions);

            // Farm Manager
            $farmManager = Role::firstOrCreate(['name' => 'farm_manager', 'guard_name' => $guardName]);
            $farmManager->syncPermissions([
                'view_birds','create_birds','edit_birds','delete_birds','manage_birds',
                'view_eggs','create_eggs','edit_eggs','delete_eggs','manage_eggs',
                'view_mortalities','create_mortalities','edit_mortalities','delete_mortalities','manage_mortalities',
                'view_feed','create_feed','edit_feed','delete_feed','manage_feed',
                'view_inventory','view_dashboard','view_kpis',
            ]);

            // Accountant
            $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => $guardName]);
            $accountant->syncPermissions([
                'view_expenses','create_expenses','edit_expenses','delete_expenses','manage_expenses',
                'view_income','create_income','edit_income','delete_income','manage_income',
                'view_payroll','create_payroll','edit_payroll','delete_payroll','manage_payroll','generate_payroll',
                'view_dashboard','view_kpis','view_reports','generate_reports','export_reports',
            ]);

            // Sales Manager
            $salesManager = Role::firstOrCreate(['name' => 'sales_manager', 'guard_name' => $guardName]);
            $salesManager->syncPermissions([
                'view_sales','create_sales','edit_sales','delete_sales','manage_sales',
                'view_customers','create_customers','edit_customers','delete_customers','manage_customers',
                'view_orders','create_orders','edit_orders','delete_orders','manage_orders',
                'view_invoices','generate_invoices','manage_invoices',
                'manage_finances','view_dashboard','view_kpis',
            ]);

            // Inventory Manager
            $inventoryManager = Role::firstOrCreate(['name' => 'inventory_manager', 'guard_name' => $guardName]);
            $inventoryManager->syncPermissions([
                'view_inventory','create_inventory','edit_inventory','delete_inventory','manage_inventory',
                'view_suppliers','create_suppliers','edit_suppliers','delete_suppliers','manage_suppliers',
                'view_feed','create_feed','edit_feed','delete_feed','manage_feed',
                'view_medicine_logs','create_medicine_logs','edit_medicine_logs','delete_medicine_logs','manage_medicine_logs',
                'view_dashboard','view_kpis',
            ]);

            // Veterinarian
            $veterinarian = Role::firstOrCreate(['name' => 'veterinarian', 'guard_name' => $guardName]);
            $veterinarian->syncPermissions([
                'view_health_checks','create_health_checks','edit_health_checks','delete_health_checks','manage_health_checks',
                'view_diseases','create_diseases','edit_diseases','delete_diseases','manage_diseases',
                'view_vaccination_logs','create_vaccination_logs','edit_vaccination_logs','delete_vaccination_logs','manage_vaccination_logs',
                'view_medicine_logs','create_medicine_logs','edit_medicine_logs','delete_medicine_logs','manage_medicine_logs',
                'view_dashboard','view_kpis',
            ]);

            // Labourer
            $labourer = Role::firstOrCreate(['name' => 'labourer', 'guard_name' => $guardName]);
            $labourer->syncPermissions([
                'view_birds','view_eggs','view_mortalities','view_feed',
                'create_mortalities','create_eggs','view_dashboard','view_kpis',
            ]);
        });

        // Clear permission cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
