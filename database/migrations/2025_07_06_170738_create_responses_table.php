<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'responses' para registrar las respuestas a los documentos.
     */
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.

            // Clave foránea a la tabla 'documents'.
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');

            $table->string('subject'); // Asunto de la respuesta.
            $table->text('summary')->nullable(); // Resumen de la respuesta, puede ser nulo.

            // Clave foránea a la tabla 'files' para el archivo de respuesta.
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');

            // Clave foránea a la tabla 'offices' para la oficina de destino de la respuesta.
            $table->foreignId('destination_office_id')->constrained('offices')->onDelete('cascade');

            $table->boolean('is_final_response')->default(false); // Indica si es la respuesta final.
            $table->date('date'); // Fecha de la respuesta.
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'responses' si la migración es revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
