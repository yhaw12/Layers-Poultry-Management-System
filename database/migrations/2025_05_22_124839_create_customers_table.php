<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('contact')->nullable();
        $table->timestamps();
         });
         Schema::table('customers', function (Blueprint $table) {
    $table->text('purchase_history')->nullable();
    $table->decimal('credit_limit', 10, 2)->default(0);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
