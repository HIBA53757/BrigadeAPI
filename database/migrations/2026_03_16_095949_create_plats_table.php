<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plats', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 100);
    $table->text('description')->nullable();
    $table->decimal('prix', 8, 2);
    $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // Relation 1-N
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Admin créateur
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plats');
    }
};
