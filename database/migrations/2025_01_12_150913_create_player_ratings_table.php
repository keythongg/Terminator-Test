<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // ID korisnika koji daje ocjenu
            $table->foreignId('rated_id')->constrained('users')->onDelete('cascade'); // ID korisnika koji dobiva ocjenu
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // ID termina
            $table->tinyInteger('rating')->unsigned(); // Ocjena (1-5)
            $table->text('comment')->nullable(); // Opcionalan komentar
            $table->timestamps(); // Kreira created_at i updated_at stupce
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_ratings');
    }
}
