<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('web/songs/{id}', 'Web\SongController@show');
Route::get('web/legal/terms', 'Web\LegalController@terms');
Route::get('web/legal/privacy', 'Web\LegalController@privacy');

Route::get('/', function () {
    $index = public_path() . '/dist/index.html';

    if (\File::exists($index)) {
        return \File::get($index);
    }

    return view('welcome');
});
