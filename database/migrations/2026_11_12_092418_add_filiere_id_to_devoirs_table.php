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
        Schema::table('devoirs', function (Blueprint $table) {
            $table->foreignId('filiere_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devoirs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('filiere_id');
        });
    }
};
