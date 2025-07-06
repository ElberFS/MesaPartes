<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'documents' para registrar los documentos del sistema.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.
            $table->string('code')->unique(); // Código único del documento.
            $table->string('subject'); // Asunto del documento.
            $table->enum('origin_type', ['internal', 'external']); // Tipo de origen: interno o externo.

            // Clave foránea a la tabla 'offices' para la oficina de origen.
            $table->foreignId('origin_office_id')->constrained('offices')->onDelete('cascade');

            // Clave foránea a la tabla 'external_people' para el origen externo, puede ser nulo.
            $table->foreignId('external_person_id')->nullable()->constrained('external_people')->onDelete('set null');

            $table->string('reference')->nullable(); // Referencia del documento, puede ser nulo.
            $table->string('origin_in_charge')->nullable(); // Encargado de origen, puede ser nulo.
            $table->text('summary')->nullable(); // Resumen del documento, puede ser nulo.

            // Clave foránea a la tabla 'files' para el archivo adjunto.
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');

            // Clave foránea a la tabla 'priorities'.
            $table->foreignId('priority_id')->constrained('priorities')->onDelete('restrict'); // Restringe eliminación si hay documentos asociados.

            $table->date('registration_date'); // Fecha de registro del documento.
            $table->enum('status', ['in_process', 'responded', 'archived'])->default('in_process'); // Estado del documento.
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'documents' si la migración es revertida, asegurando el orden de eliminación de FK.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
