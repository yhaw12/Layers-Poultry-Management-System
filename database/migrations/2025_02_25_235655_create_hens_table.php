<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHensTable extends Migration
{
    public function up()
    {
        Schema::create('hens', function (Blueprint $table) {
            $table->id();
            $table->string('breed');
            $table->integer('quantity');
            $table->integer('working')->default(0); // Hens currently laying
            $table->integer('age'); // In months
            $table->date('entry_date');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hens');
    }
}