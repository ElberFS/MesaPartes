<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'priorities' para definir los niveles de prioridad de los documentos.
     */
    public function up(): void
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.
            $table->string('level')->unique(); // Nivel de prioridad (e.g., 'Alta', 'Media', 'Baja'), debe ser único.
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'priorities' si la migración es revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('priorities');
    }
};
