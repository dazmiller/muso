<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('thread_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('thread_id')->references('id')->on('threads');
            
            // Same user can only join the thread once
            $table->unique(['user_id', 'thread_id'], 'thread_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
