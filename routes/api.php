<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'cors'], function(){

    Route::group(['prefix' => 'v1'], function(){

        Route::post('auth/signup', 'Api\AuthController@signup');
        Route::post('auth/facebook', 'Api\AuthController@facebook');
        Route::post('auth/twitter', 'Api\AuthController@twitter');
        Route::post('auth/login', 'Api\AuthController@authenticate');
        Route::get('auth/unlink/{provider}', ['middleware' => 'auth', 'uses' => 'Api\AuthController@unlink']);
        Route::delete('auth/logout', 'Api\AuthController@logout');
        Route::post('auth/forgot', 'Api\AuthController@forgot');
        Route::get('auth/recover/{token}', 'Api\AuthController@recover');

        Route::get('files/{id}', 'Api\FileController@show');
        Route::get('discovers', 'Api\DiscoverController@index');
        Route::get('discovers/tag/{tag}', 'Api\DiscoverController@tag');
        Route::get('discovers/genre/{genre}', 'Api\DiscoverController@genre');
        Route::get('discovers/users/{song_id}/liked', 'Api\DiscoverController@liked');
        Route::get('songs/{id}', 'Api\SongController@show');
        Route::get('albums/{id}/published', 'Api\AlbumController@published');
        Route::get('artists', 'Api\ArtistController@index');
        Route::get('artists/{user_id}/albums', 'Api\ArtistController@albums');
        Route::get('genres', 'Api\GenreController@index');
        Route::get('blog', 'Api\BlogController@index');
        Route::get('blog/{id}', 'Api\BlogController@show');
        Route::get('tags/song', 'Api\TagController@song');

        Route::get('i', 'Api\InstrumentsController@index');
        Route::get('t', 'Api\TestController@index');
        
        Route::get('search/songs', 'Api\SearchController@songs');
        Route::get('search/albums', 'Api\SearchController@albums');
        Route::get('search/artists', 'Api\SearchController@artists');

        Route::group(['middleware' => 'jwt.auth'], function(){
            Route::get('albums', 'Api\AlbumController@index');
            Route::post('albums', 'Api\AlbumController@store');
            Route::get('albums/{id}', 'Api\AlbumController@show');
            Route::post('albums/{id}', 'Api\AlbumController@update'); //We can't use PUT because PHP doesn't support PUT to upload files :(
            Route::delete('albums/{id}', 'Api\AlbumController@destroy');

            Route::get('songs/{id}/likeable', 'Api\SongController@likeable');
            Route::get('songs/{id}/play', 'Api\FileController@countPlays');
            Route::get('songs/{id}/download', 'Api\FileController@download');

            Route::post('albums/{album_id}/songs', 'Api\SongController@store');
            Route::post('albums/{album_id}/songs/{id}', 'Api\SongController@update');  //We can't use PUT because PHP doesn't support PUT to upload files :(
            Route::delete('albums/{album_id}/songs/{id}', 'Api\SongController@destroy');

            Route::get('users', 'Api\UserController@index');
            Route::get('users/current', 'Api\UserController@current');
            Route::get('users/countries', 'Api\UserController@countries');
            Route::get('users/{id}', 'Api\UserController@show');
            Route::post('users/{id}', 'Api\UserController@update'); //We can't use PUT because PHP doesn't support PUT to upload files :(
            Route::get('users/{id}/feed', 'Api\UserController@feed');
            Route::delete('users/{id}', 'Api\UserController@destroy');
            Route::get('users/{user_id}/followers', 'Api\FollowerController@followers');
            Route::get('users/{user_id}/followings', 'Api\FollowerController@followings');
            Route::get('users/{user_id}/follow', 'Api\FollowerController@follow');
            Route::get('users/{user_id}/unfollow', 'Api\FollowerController@unfollow');

            // Route::get('genres', 'Api\GenreController@index');
            Route::post('genres', 'Api\GenreController@store');
            Route::put('genres/{id}', 'Api\GenreController@update');
            Route::delete('genres/{id}', 'Api\GenreController@destroy');

            Route::get('comments', 'Api\CommentController@index');
            Route::get('comments/{id}', 'Api\CommentController@show');
            Route::put('comments/{id}', 'Api\CommentController@update');
            Route::delete('comments/{id}', 'Api\CommentController@destroy');
            Route::post('comments', 'Api\CommentController@store');

            Route::get('playlists', 'Api\PlaylistController@index');
            Route::post('playlists', 'Api\PlaylistController@store');
            Route::post('playlists/{id}', 'Api\PlaylistController@add');
            Route::put('playlists/{id}', 'Api\PlaylistController@update');
            Route::get('playlists/favorites', 'Api\PlaylistController@favorites');
            Route::get('playlists/history', 'Api\PlaylistController@history');
            Route::get('playlists/{id}', 'Api\PlaylistController@show');
            Route::delete('playlists/{id}', 'Api\PlaylistController@destroy');
            Route::delete('playlists/{playlist_id}/song/{id}', 'Api\PlaylistController@remove');

            Route::get('posts', 'Api\PostController@index');
            Route::get('posts/{id}', 'Api\PostController@show');
            Route::post('posts', 'Api\PostController@store');
            Route::post('posts/{id}', 'Api\PostController@update');
            Route::delete('posts/{id}', 'Api\PostController@destroy');
            
            Route::get('mailbox/messages/received', 'Api\MessageController@received');
            Route::get('mailbox/messages/unread', 'Api\MessageController@unread');
            Route::get('mailbox/messages/sent', 'Api\MessageController@sent');
            Route::post('mailbox/messages', 'Api\MessageController@store');
            Route::put('mailbox/messages/{id}', 'Api\MessageController@update');
            Route::get('mailbox/messages/{id}', 'Api\MessageController@show');
            Route::get('mailbox/users', 'Api\MessageController@users');
            
            Route::get('statistics', 'Api\StatisticsController@index');
            Route::get('statistics/overview', 'Api\StatisticsController@overview');
            Route::get('statistics/popular/artists', 'Api\StatisticsController@artists');
            Route::get('statistics/popular/albums', 'Api\StatisticsController@albums');
            Route::get('statistics/popular/songs', 'Api\StatisticsController@songs');

            Route::get('configurations', 'Api\ConfigurationController@index');
            Route::post('configurations', 'Api\ConfigurationController@store');
            Route::post('configurations/theme/apply', 'Api\ConfigurationController@apply');
            Route::post('configurations/theme/clear', 'Api\ConfigurationController@clear');

        });
    });

    Route::group(['prefix' => 'installer', 'middleware' => ['installed']], function(){

        Route::get('requirements', 'Installer\RequirementsController@check');
        Route::get('permissions', 'Installer\PermissionsController@check');
        Route::post('database', 'Installer\DatabaseController@check');
        Route::post('migrate', 'Installer\DatabaseController@migrate');
        Route::post('admin', 'Installer\DatabaseController@admin');
        Route::post('environment', 'Installer\EnvironmentController@store');
        Route::get('appkey', 'Installer\EnvironmentController@appkey');
        Route::get('jwtkey', 'Installer\EnvironmentController@jwtkey');
        Route::get('geoip', 'Installer\OtherTasksController@geoip');
        Route::get('link', 'Installer\OtherTasksController@link');
        Route::post('config', 'Installer\OtherTasksController@configs');
        Route::delete('remove', 'Installer\OtherTasksController@remove');

    });
});
