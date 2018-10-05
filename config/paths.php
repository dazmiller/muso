<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem Paths
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many paths as you wish, this paths will be used
    | to store the files that are uploaded using the FileController, this way
    | you can control where to save different files based on the giving model, for example
    | the user's avatar should be in a different location then album's images or tracks.
    |
    | IMPORTANT: If you add more keys to replace, go ahead and modify the FileController
    | to actually replace those values.
    |
    */

    'album'         => [
        'image'     => 'users/{user_id}/albums/{album_id}/art',
        'track'     => 'users/{user_id}/albums/{album_id}/tracks',
    ],
    'post'          => [
        'asset'     => 'users/{user_id}/posts/{post_id}'
    ],
    'user'          => [
        'avatar'    => 'users/{user_id}/avatars',
        'banner'    => 'users/{user_id}/banners',
    ],

];