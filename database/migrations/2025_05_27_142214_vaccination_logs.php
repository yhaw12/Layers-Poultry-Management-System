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
        Schema::create('vaccination_logs', function (Blueprint $table) {
    $table->id();
    // $table->foreignId('bird_id')->constrained()->onDelete('cascade');
    $table->string('vaccine_name');
    $table->date('date_administered');
    $table->date('next_vaccination_date')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
     $table->softDeletes();
    
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_logs');
    }
};
