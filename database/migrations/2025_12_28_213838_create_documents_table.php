<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de documentos
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();     // D-0001-2025-SG
            $table->string('subject');            // Asunto
            $table->text('description')->nullable(); // Descripción
            $table->string('file_path');          // Ruta del PDF
            $table->foreignId('office_id')->constrained()->cascadeOnDelete(); // Oficina origen
            $table->foreignId('expediente_id')->nullable()
                  ->constrained()->nullOnDelete(); // Puede pertenecer a un expediente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
