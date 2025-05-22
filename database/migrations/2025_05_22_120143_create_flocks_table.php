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
          Schema::create('flocks', function (Blueprint $table) {
        $table->id();
        $table->string('name');                // e.g. “Flock A”
        $table->string('strain');              // “Isa Brown”
        $table->date('start_date');            // when purchased
        $table->string('source');              // Hatchery details
        $table->integer('initial_count');      // starting headcount
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flocks');
    }
};
