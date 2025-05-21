<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEggsTable extends Migration
{
    public function up()
    {
        Schema::create('eggs', function (Blueprint $table) {
            $table->id();
            $table->integer('crates'); // Number of crates collected daily
            $table->date('date_laid');
            $table->integer('sold_quantity')->default(0);
            $table->date('sold_date')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('eggs');
    }
}