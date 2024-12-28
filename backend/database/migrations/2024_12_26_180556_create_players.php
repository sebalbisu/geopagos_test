<?php

use App\Enums\Gender;
use App\Services\EnumService;
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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->integer('tournament_id');
            $table->integer('dni');
            $table->enum('gender', Gender::values());
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');
            $table->decimal('handicap', 5, 2);
            $table->decimal('skill', 5, 2);
            $table->decimal('strength', 5, 2)->nullable();
            $table->decimal('speed', 5, 2)->nullable();
            $table->decimal('latency', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('tournament_id')->references('id')->on('tournaments');
            $table->unique(['tournament_id', 'dni']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
