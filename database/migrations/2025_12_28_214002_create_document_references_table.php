<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Referencias entre documentos y/o expedientes
     */
    public function up(): void
    {
        Schema::create('document_references', function (Blueprint $table) {
            $table->id();

            $table->string('source_type'); // document | expediente
            $table->unsignedBigInteger('source_id');

            $table->string('target_type'); // document | expediente
            $table->unsignedBigInteger('target_id');

            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_references');
    }
};
