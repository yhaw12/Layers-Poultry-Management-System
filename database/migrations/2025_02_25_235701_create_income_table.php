<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeTable extends Migration
{
    public function up()
    {
        Schema::create('income', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('income');
    }
}