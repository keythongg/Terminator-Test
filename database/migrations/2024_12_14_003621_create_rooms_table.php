<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('location_id')->constrained();
            $table->date('date');  // Samo datum
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_players');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('password')->nullable();
            $table->timestamps();
        });
        
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
