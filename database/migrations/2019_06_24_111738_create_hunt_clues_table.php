<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuntCluesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hunt_clues', function ($collection) {
            $collection->index('hunt_complexity_id');
            $collection->index('game_id');
            $collection->index('game_variation_id');
            $collection->geospatial('location.coordinates', '2dsphere');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hunt_clues');
    }
}
