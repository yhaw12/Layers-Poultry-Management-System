<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::table('feed', function (Blueprint $table) {
            // Link to the expenses table. Nullable because some feeds might not be expenses.
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('feed', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropColumn('expense_id');
        });
    }
};
