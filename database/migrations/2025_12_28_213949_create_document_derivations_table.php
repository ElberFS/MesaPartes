<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Derivaciones de documentos entre oficinas
     */
    public function up(): void
    {
        Schema::create('document_derivations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_office_id')->constrained('offices');
            $table->foreignId('to_office_id')->constrained('offices');
            $table->timestamp('sent_at'); // Fecha y hora de envío
            $table->string('status');     // enviado, recibido, atendido, de_conocimiento
            $table->text('comment')->nullable(); // Comentarios en español
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_derivations');
    }
};
