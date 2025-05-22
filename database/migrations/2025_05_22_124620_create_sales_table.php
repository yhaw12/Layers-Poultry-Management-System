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
        
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date_sold');
            $table->integer('quantity');        // crates or units sold
            $table->decimal('unit_price',8,2);
            $table->decimal('revenue', 10, 2)->nullable();   // price per crate/unit
            $table->string('customer_name');
            $table->string('customer_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
