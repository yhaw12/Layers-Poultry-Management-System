<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\AlertFactory;
use Database\Factories\BirdFactory;
use Database\Factories\ChickFactory;
use Database\Factories\CustomerFactory;
use Database\Factories\EggFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\ExpenseFactory;
use Database\Factories\FeedFactory;
use Database\Factories\FeedConsumptionFactory;
use Database\Factories\IncomeFactory;
use Database\Factories\InventoryFactory;
use Database\Factories\MedicineLogFactory;
use Database\Factories\MortalitiesFactory;
use Database\Factories\OrderFactory;
use Database\Factories\PayrollFactory;
use Database\Factories\SaleFactory;
use Database\Factories\SupplierFactory;
use Database\Factories\UserFactory;
use Database\Factories\UserActivityLogFactory;
use Database\Factories\VaccinationLogFactory;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Customer;
use App\Models\Sale;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call UserSeeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Create additional random users
        UserFactory::new()->count(10)->create();

        // Create Suppliers
        $suppliers = SupplierFactory::new()->count(10)->create();

        // Create Birds
        $birds = BirdFactory::new()->count(5000)->create();

        // Create Chicks
        ChickFactory::new()->count(5024)->create();

        // Create Eggs
        $eggs = EggFactory::new()->count(2000)->create();

        // Create Feed
        $feeds = FeedFactory::new()->count(50)->create();

        // Create Feed Consumption
        foreach ($feeds->random(20) as $feed) {
            FeedConsumptionFactory::new()->count(5)->create(['feed_id' => $feed->id]);
        }

        // Create Medicine Logs
        MedicineLogFactory::new()->count(20)->create(['type' => 'purchase']);
        MedicineLogFactory::new()->count(20)->create(['type' => 'consumption']);

        // Create Mortalities
        MortalitiesFactory::new()->count(30)->create();

        // Create Inventory
        InventoryFactory::new()->count(20)->create();

        // Create Expenses
        ExpenseFactory::new()->count(50)->create();

        // Create Income
        IncomeFactory::new()->count(100)->create();

        // Create Employees
        $employees = EmployeeFactory::new()->count(15)->create();

        // Create Payroll
        foreach ($employees as $employee) {
            PayrollFactory::new()->count(5)->create(['employee_id' => $employee->id]);
        }

        // Create Customers
        $customers = CustomerFactory::new()->count(20)->create();

        // Create Sales
        $saleables = $birds->merge($eggs);
        foreach ($customers as $customer) {
            for ($i = 0; $i < 3; $i++) {
                $saleable = $saleables->random();
                SaleFactory::new()->create([
                    'customer_id' => $customer->id,
                    'saleable_id' => $saleable->id,
                    'saleable_type' => get_class($saleable),
                ]);
            }
        }

        // Create Vaccination Logs
        VaccinationLogFactory::new()->count(40)->create();

        // Create Orders
        foreach ($customers as $customer) {
            OrderFactory::new()->count(8)->create();
        }

        // Create Alerts
        AlertFactory::new()->count(15)->create();

        // Create Activity Logs
        UserActivityLogFactory::new()->count(50)->create();
    }
}