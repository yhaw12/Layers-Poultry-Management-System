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
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('weight', 10, 2);
            $table->date('purchase_date');
            $table->decimal('cost', 10, 2)->default(0);;
            $table->timestamp('synced_at')->nullable();
            $table->foreignId('bird_id')->nullable()->constrained('birds')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();


            $table->index('purchase_date');
            $table->index(['supplier_id']);
        });
    }

    public function down(): void
    {
        // Drop foreign keys first for safety (only if table exists)
        if (Schema::hasTable('feed')) {
            Schema::table('feed', function (Blueprint $table) {
                if (Schema::hasColumn('feed', 'supplier_id')) {
                    $table->dropForeign(['supplier_id']);
                }
                if (Schema::hasColumn('feed', 'bird_id')) {
                    $table->dropForeign(['bird_id']);
                }
            });

            Schema::dropIfExists('feed');
        }
    }
}
