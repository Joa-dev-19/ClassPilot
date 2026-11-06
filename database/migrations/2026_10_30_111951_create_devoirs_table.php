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
        Schema::create('devoirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->restrictOnDelete();
            $table->string('titre');
            $table->text('consignes')->nullable();
            $table->date('date_limite');
            $table->string('statut')->default('brouillon');
            $table->timestamps();

            $table->index(['enseignant_id', 'date_limite']);
            $table->index(['statut', 'date_limite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoirs');
    }
};
