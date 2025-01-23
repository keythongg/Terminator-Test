<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pokreni migraciju.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Naziv grada
            $table->timestamps();
        });
    }

    /**
     * Vrati migraciju unazad.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
