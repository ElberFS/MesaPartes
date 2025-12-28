<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        schema::create('offices', function (Blueprint $table) {
            $table->id();
            // Nombre completo de la oficina
            $table->string('name');
            // Acrónimo o sigla (ej: MP, OTI, RRHH)
            $table->string('acronym', 20)->unique();
            // Indica si la oficina está activa o vigente
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
