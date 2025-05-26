<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('eggs', function (Blueprint $table) {
               $table->id();
               $table->integer('crates');
               $table->integer('sold_quantity')->nullable();
               $table->date('sold_date')->nullable();
               $table->decimal('sale_price', 8, 2)->nullable();
               $table->date('date_laid');
               $table->timestamp('synced_at')->nullable();
               $table->timestamps();
           });
           
       }

       public function down(): void
       {
           Schema::dropIfExists('eggs');
       }
   };
   