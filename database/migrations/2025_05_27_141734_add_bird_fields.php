<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::table('birds', function (Blueprint $table) {
            // $table->string('vaccination_status')->nullable();
            // $table->string('housing_location')->nullable();
            // $table->enum('stage', ['chick', 'grower', 'layer'])->default('chick');
        });
    }

    public function down()
    {
        Schema::table('birds', function (Blueprint $table) {
            // $table->dropColumn(['vaccination_status', 'housing_location', 'stage']);
        });
    }
};
