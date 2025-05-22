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
        Schema::create('medicine_logs', function (Blueprint $table) {
        $table->id();
        $table->string('medicine_name');
        $table->enum('type', ['purchase', 'consumption']);
        $table->float('quantity'); // Use consistent unit (e.g., ml or mg)
        $table->string('unit')->default('ml'); // Optional: 'ml', 'mg', etc.
        $table->date('date');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_logs');
    }
};
