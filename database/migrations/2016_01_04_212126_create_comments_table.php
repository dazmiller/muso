<?php

use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function ($table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('user_id')->unsigned();

            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('published')->default(true);

            
            $table->integer('lft')->nullable();
            $table->integer('rgt')->nullable();
            $table->integer('depth')->nullable();

            $table->morphs('commentable');
            

            $table->index('user_id');
            $table->index('commentable_id');
            $table->index('commentable_type');

            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
