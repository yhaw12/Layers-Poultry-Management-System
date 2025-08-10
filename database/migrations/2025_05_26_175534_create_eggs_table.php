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
            $table->integer('crates')->default(0);
            $table->integer('additional_eggs')->default(0);
            $table->integer('total_eggs')->default(0);
            $table->boolean('is_cracked')->default(false);
            $table->enum('egg_size', ['small', 'medium', 'large'])->nullable();
            $table->date('date_laid');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pen_id')->references('id')->on('pens')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eggs');
    }
};