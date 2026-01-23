<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medicine_logs', function (Blueprint $table) {
            // Create the link. Nullable because not all medicine logs come from expenses (e.g., consumption)
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('medicine_logs', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropColumn('expense_id');
        });
    }
};