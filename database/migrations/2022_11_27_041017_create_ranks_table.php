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
        if ( !Schema::hasTable('ranks' ) ){
            Schema::create('ranks', function (Blueprint $table) {
                $table->id();
                $table->Integer('guild_id')->unsigned();
                $table->smallInteger('level')->unsigned();
                $table->string('name');
                $table->timestamps();
                $table->unique(['guild_id', 'level']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('ranks');
    }
};
