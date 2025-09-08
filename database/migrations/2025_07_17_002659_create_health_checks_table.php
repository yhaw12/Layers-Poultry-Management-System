<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('birds') || !Schema::hasTable('diseases')) {
            throw new \Exception('Birds and Diseases tables must exist before creating health_checks table.');
        }

        Schema::create('health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_id')->constrained()->onDelete('cascade');
            $table->foreignId('disease_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->string('status'); // e.g., healthy, sick, recovering
            $table->text('symptoms')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_checks');
    }
};