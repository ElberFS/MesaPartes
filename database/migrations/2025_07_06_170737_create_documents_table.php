<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'documents' para registrar los documentos del sistema.
     * Se han modificado los campos relacionados con el origen externo
     * para incluir detalles de organización y eventos, usando nombres en inglés.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.
            $table->string('code')->unique(); // Código único del documento.
            $table->string('subject'); // Asunto del documento.
            $table->enum('origin_type', ['internal', 'external']); // Tipo de origen: interno o externo.

            // Clave foránea a la tabla 'offices' para la oficina de origen (para origen interno).
            // Se mantiene 'onDelete('cascade')' ya que una oficina eliminada implica que sus documentos internos también se eliminan.
            $table->foreignId('origin_office_id')->nullable()->constrained('offices')->onDelete('cascade');

            // Nuevos campos para el origen externo (en reemplazo de external_person_id), con nombres en inglés.
            $table->string('organization_name')->nullable(); // Nombre de la organización externa.
            $table->string('external_contact_person')->nullable(); // Nombre de la persona de contacto en la organización externa.
            $table->string('external_contact_role')->nullable(); // Cargo de la persona de contacto en la organización externa.

            // Campos para detalles de eventos o convenios (aplicables a origen externo), con nombres en inglés.
            $table->date('event_date')->nullable(); // Fecha específica del evento o convenio.
            $table->time('event_time')->nullable(); // Hora específica del evento o convenio.

            $table->string('reference')->nullable(); // Referencia del documento, puede ser nulo.
            $table->string('origin_in_charge')->nullable(); // Encargado de origen (general, puede ser interno o externo), puede ser nulo.
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
     * Elimina la tabla 'documents' si la migración es revertida,
     * asegurando el orden de eliminación de claves foráneas.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
