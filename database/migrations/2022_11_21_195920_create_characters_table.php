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
        Schema::create('characters', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->string('region');
            $table->integer('guild_id')->unsigned()->nullable();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->unsignedSmallInteger('realm_id');
            $table->unsignedSmallInteger('playable_race_id');
            $table->unsignedSmallInteger('playable_class_id');
            $table->unsignedSmallInteger('level');
            
            // Setting unique combination
            $table->unique(['id', 'region']);

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
    }
};
