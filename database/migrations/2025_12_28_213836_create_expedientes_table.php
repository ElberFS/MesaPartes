<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de expedientes (conjunto de documentos)
     */
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();     // E-0001-2025-SG
            $table->string('subject');            // Asunto
            $table->text('description')->nullable(); // Descripción
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
