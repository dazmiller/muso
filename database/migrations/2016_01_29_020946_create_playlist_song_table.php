<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistSongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlist_song', function (Blueprint $table) {
            $table->integer('playlist_id')->unsigned();
            $table->integer('song_id')->unsigned();
            $table->integer('sorting')->unsigned();
            $table->timestamps();

            $table->unique( array('playlist_id','song_id') );
            
            $table->foreign('playlist_id')->references('id')->on('playlists');
            $table->foreign('song_id')->references('id')->on('songs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('playlist_song');
    }
}
