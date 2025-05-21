<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChicksTable extends Migration
{
    public function up()
    {
        Schema::create('chicks', function (Blueprint $table) {
            $table->id();
            $table->string('breed');
            $table->integer('quantity_bought');
            $table->decimal('feed_amount', 10, 2)->default(0); // Feed in kg
            $table->integer('alive')->default(0);
            $table->integer('dead')->default(0);
            $table->date('purchase_date');
            $table->decimal('cost', 10, 2);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chicks');
    }
}