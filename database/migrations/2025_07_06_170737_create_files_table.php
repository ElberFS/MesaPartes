<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'files' para almacenar información sobre los archivos adjuntos.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.
            $table->string('original_name'); // Nombre original del archivo.
            $table->string('path'); // Ruta de almacenamiento del archivo (local o en la nube).
            $table->string('file_type'); // Tipo de archivo (e.g., 'application/pdf', 'application/msword').
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'files' si la migración es revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
