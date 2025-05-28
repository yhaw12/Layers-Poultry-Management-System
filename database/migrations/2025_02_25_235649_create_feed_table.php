<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedTable extends Migration
{
    public function up()
    {
        Schema::create('feed', function (Blueprint $table) {
            $table->id();
            $table->string('type'); 
            $table->string('supplier');
            $table->integer('quantity'); 
            $table->decimal('weight', 10, 2);
            $table->date('purchase_date');
            $table->decimal('cost', 10, 2);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('feed');
    }
}