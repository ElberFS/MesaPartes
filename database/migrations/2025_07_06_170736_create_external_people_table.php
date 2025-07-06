<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'external_people' para almacenar información de personas o entidades externas.
     */
    public function up(): void
    {
        Schema::create('external_people', function (Blueprint $table) {
            $table->id(); // Columna 'id' auto-incremental como clave primaria.
            $table->string('name'); // Nombre de la persona o entidad externa.
            $table->string('position')->nullable(); // Cargo de la persona, puede ser nulo.
            $table->string('company')->nullable(); // Empresa a la que pertenece, puede ser nulo.
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'external_people' si la migración es revertida.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_people');
    }
};
