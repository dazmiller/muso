<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('album_id')->unsigned();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('lyric')->nullable();
            $table->time('duration')->nullable();

            //total fields to query the database easier and faster
            //this will avoid doing the counting every time in the database
            $table->integer('total_plays')->default(0)->index();
            $table->integer('total_favorites')->default(0)->index();

            $table->integer('sorting')->default(0);

            $table->foreign('album_id')->references('id')->on('albums');
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
        Schema::drop('songs');
    }
}
