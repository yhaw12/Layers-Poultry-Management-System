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
               $table->unsignedBigInteger('pen_id')->nullable();
               $table->decimal('crates', 8, 2)->default(0);
               $table->integer('small_eggs')->default(0);
               $table->integer('medium_eggs')->default(0);
               $table->integer('large_eggs')->default(0);
               $table->integer('cracked_eggs')->default(0);
               $table->unsignedBigInteger('collected_by')->nullable();
               $table->date('date_laid');
               $table->unsignedBigInteger('created_by')->nullable();
               $table->timestamps();
               $table->softDeletes();

               $table->foreign('pen_id')->references('id')->on('pens')->onDelete('set null');
               $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
               $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('eggs');
       }
   };
   