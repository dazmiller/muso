<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('genre_id')->unsigned();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('published')->default(false);
            $table->time('duration')->nullable(); //The sum of all tracks

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('genre_id')->references('id')->on('genres');
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
        Schema::drop('albums');
    }
}
