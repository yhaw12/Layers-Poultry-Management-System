<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedConsumptionTable extends Migration
{
    public function up()
    {
        Schema::create('feed_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->constrained('feed')->onDelete('cascade');
            $table->date('date');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_consumption');
    }
}