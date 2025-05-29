<?php

use App\Models\Mortalities;
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
        Schema::create('birds', function (Blueprint $table) {
            $table->id();
            $table->string('breed');
            $table->enum('type', ['layer', 'broiler']);
            $table->integer('quantity');
            $table->boolean('working')->default(true);
            $table->integer('age');
            $table->date('entry_date');
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();
        });

    }

//     public function mortalities() {
//     return $this->hasMany(Mortalities::class);
// }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birds');
    }
};



