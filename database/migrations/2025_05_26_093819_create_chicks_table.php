<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChicksTable extends Migration
{
    public function up(): void
    {
        // Schema::create('chicks', function (Blueprint $table) {
        //     // $table->id();
        //     // $table->string('breed');
        //     // $table->integer('quantity_bought');
        //     // $table->decimal('feed_amount', 8, 2)->default(0);
        //     // $table->integer('alive');
        //     // $table->integer('dead')->default(0);
        //     // $table->date('purchase_date');
        //     // $table->decimal('cost', 8, 2);
        //     // $table->dateTime('synced_at')->nullable();
        //     // $table->timestamps();
        //     //  $table->softDeletes();
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('chicks');
    }
}