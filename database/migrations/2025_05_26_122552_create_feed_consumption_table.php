<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedConsumptionTable extends Migration
{
    public function up()
    {
        Schema::create('feed_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_id')->nullable();
            $table->date('date');
            $table->decimal('quantity', 8, 2);
            $table->integer('threshold')->default(100);
            $table->timestamps();

            $table->foreign('feed_id')->references('id')->on('feed')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_consumptions');
    }
}