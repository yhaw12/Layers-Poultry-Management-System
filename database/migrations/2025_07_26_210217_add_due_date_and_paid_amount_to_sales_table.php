<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueDateAndPaidAmountToSalesTable extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('sale_date');
            $table->decimal('paid_amount', 8, 2)->default(0)->after('total_amount');
            $table->enum('status', ['pending', 'paid', 'partially_paid', 'overdue'])->default('pending')->change();
            $table->foreignId('created_by')->nullable()->after('product_variant')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'paid_amount']);
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending')->change();
             $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
}