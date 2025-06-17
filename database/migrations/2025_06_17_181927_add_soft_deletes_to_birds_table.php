<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToBirdsTable extends Migration
{
    public function up()
    {
        Schema::table('birds', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('birds', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}