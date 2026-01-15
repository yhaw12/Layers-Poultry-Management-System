<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birds', function (Blueprint $table) {
            $table->id();
            $table->string('breed');
            $table->enum('type', ['layer', 'broiler']);
            $table->integer('quantity');
            $table->integer('quantity_bought')->nullable();
            $table->decimal('feed_amount', 8, 2)->nullable()->default(0);
            $table->integer('alive')->nullable();
            $table->integer('dead')->nullable()->default(0);
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->boolean('working')->default(true);
            $table->integer('age');
            $table->boolean('vaccination_status')->nullable();
            $table->unsignedBigInteger('pen_id')->nullable();
            $table->enum('stage', ['chick', 'juvenile', 'adult']);
            $table->date('entry_date');
            $table->dateTime('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pen_id')->references('id')->on('pens')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birds');
    }
};