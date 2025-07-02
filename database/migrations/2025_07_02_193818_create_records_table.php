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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_import_id')->constrained()->cascadeOnDelete();
            $table->string('record_type', 2);
            $table->unsignedInteger('line_number');
            $table->json('data');
            $table->timestamps();

            $table->unique(['file_import_id', 'record_type', 'line_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
