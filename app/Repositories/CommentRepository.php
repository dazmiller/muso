<?php

namespace App\Repositories;

use App\Comment;
use Config;
use DB;
use Log;

class CommentRepository{

    public function getAllComments($page = 0){
        $pageSize = Config::get('app.page_size');

        return Comment::select(DB::raw("
                        comments.id, comments.title, comments.body, comments.created_at, comments.published,
                        users.id as user_id, users.name, users.image
                    "))
                    ->join('users','users.id','=','comments.user_id')
                    ->orderBy('created_at','DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
    }
}