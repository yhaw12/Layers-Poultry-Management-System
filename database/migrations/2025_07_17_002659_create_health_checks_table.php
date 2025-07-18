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
        Schema::create('health_checks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bird_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->string('status'); // e.g., healthy, sick, recovering
        $table->text('symptoms')->nullable();
        $table->text('treatment')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_checks');
    }
};
