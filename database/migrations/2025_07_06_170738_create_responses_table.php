<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'responses' para registrar las respuestas,
     * con campos detallados para el destino (interno/externo).
     */
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.

            // Clave foránea al documento original al que responde.
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');

            $table->string('subject'); // Asunto de la respuesta.
            $table->text('summary')->nullable(); // Resumen de la respuesta, puede ser nulo.

            // Clave foránea a la tabla 'files' para el archivo de respuesta.
            $table->foreignId('file_id')->nullable()->constrained('files')->onDelete('set null'); // Puede ser nulo y se establece a null si el archivo se elimina.

            // --- CAMPOS DE DESTINO DE LA RESPUESTA (NUEVOS/REORGANIZADOS) ---
            $table->enum('destination_type', ['internal', 'external']); // Tipo de destino de la respuesta: interno o externo.

            // Clave foránea a la tabla 'offices' para la oficina de destino de la respuesta (para destino interno).
            $table->foreignId('destination_office_id')->nullable()->constrained('offices')->onDelete('cascade');

            // Campos para el destino externo de la respuesta.
            $table->string('destination_organization_name')->nullable(); // Nombre de la organización externa de destino.
            $table->string('destination_contact_person')->nullable(); // Nombre de la persona de contacto en la organización externa de destino.
            $table->string('destination_contact_role')->nullable(); // Cargo de la persona de contacto en la organización externa de destino.
            // --- FIN CAMPOS DE DESTINO ---

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
