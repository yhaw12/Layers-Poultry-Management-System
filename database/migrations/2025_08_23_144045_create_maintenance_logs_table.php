<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('maintenance_logs', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('task'); // e.g., sawdust_change, water_system_check, etc.
$table->dateTime('performed_at');
$table->text('notes')->nullable();
$table->timestamps();


$table->index(['task', 'performed_at']);
});
}


public function down(): void
{
Schema::dropIfExists('maintenance_logs');
}
};