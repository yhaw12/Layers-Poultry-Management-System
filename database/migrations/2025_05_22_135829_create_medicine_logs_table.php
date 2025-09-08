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
        $table->enum('type', ['purchase', 'consumption'])->nullable();;
        $table->float('quantity'); 
        $table->string('unit')->default('ml'); 
        $table->date('date');
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
        Schema::dropIfExists('medicine_logs');
    }
};


//   return new class extends Migration
//    {
//        public function up(): void
//        {
//            Schema::table('medicine_logs', function (Blueprint $table) {
//                $table->enum('type', ['purchase', 'consumption'])->change();
//                $table->decimal('quantity', 10, 2)->change();
//                $table->string('unit', 50)->change();
//            });
//        }

//        public function down(): void
//        {
//            Schema::table('medicine_logs', function (Blueprint $table) {
//                $table->string('type')->nullable()->change();
//                $table->float('quantity')->change();
//                $table->string('unit')->default('ml')->change();
//            });
//        }
//    };
