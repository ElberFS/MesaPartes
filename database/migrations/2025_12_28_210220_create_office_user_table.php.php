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
        Schema::create('office_user', function (Blueprint $table) {

            // Usuario
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Oficina
            $table->foreignId('office_id')
                ->constrained()
                ->cascadeOnDelete();

            // Indica si el usuario es jefe de la oficina
            $table->boolean('is_boss')->default(false);

            // Fecha de asignación a la oficina
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            // Evita duplicar usuario en la misma oficina
            $table->unique(['user_id', 'office_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_user');
    }
};
