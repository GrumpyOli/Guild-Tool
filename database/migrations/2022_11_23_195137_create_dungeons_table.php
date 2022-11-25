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
        Schema::create('dungeons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('character_id');
            $table->string('dungeon');
            $table->string('short_name');
            $table->unsignedSmallInteger('mythic_level');
            $table->timestamp('completed_at');
            $table->unsignedInteger('clear_time_ms');
            $table->unsignedInteger('par_time_ms');
            $table->unsignedInteger('num_keystone_upgrades');
            $table->unsignedInteger('map_challenge_mode_id');
            $table->unsignedInteger('zone_id');
            $table->string('href');
            $table->float('score');
            $table->timestamps();

            // Setting uniques values
            $table->unique(['character_id', 'clear_time_ms', 'dungeon']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dungeons');
    }
};
