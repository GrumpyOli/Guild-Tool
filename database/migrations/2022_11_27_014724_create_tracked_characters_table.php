<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tracked_characters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('guild_id');
            $table->unsignedInteger('character_id');
            $table->timestamps();
            $table->index('guild_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('tracked_characters');
    }
};
