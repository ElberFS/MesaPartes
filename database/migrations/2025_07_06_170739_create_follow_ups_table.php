<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'follow_ups' para el seguimiento del recorrido físico de documentos y respuestas.
     */
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.

            // Clave foránea a la tabla 'documents'.
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');

            // Clave foránea a la tabla 'responses', puede ser nulo si el seguimiento es del documento inicial.
            $table->foreignId('response_id')->nullable()->constrained('responses')->onDelete('set null');

            // Clave foránea a la tabla 'offices' para la oficina actual del documento/respuesta.
            $table->foreignId('current_office_id')->constrained('offices')->onDelete('cascade');

            $table->text('observation')->nullable(); // Observaciones del seguimiento, puede ser nulo.
            $table->date('date'); // Fecha del seguimiento.
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'follow_ups' si la migración es revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
