<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register','AuthController@register');
Route::post('/login','AuthController@login');

Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::post('logout','AuthController@logout');
    Route::get('/user','AuthController@user');
    Route::put('/user','AuthController@update');

    Route::get('/posts','PostController@index');
    Route::post('/posts','PostController@store');
    Route::get('/posts/{id}','PostController@show');
    Route::put('/posts/{id}','PostController@update');
    Route::delete('/posts/{id}','PostController@destroy');

    // Comment
    Route::get('/posts/{id}/comments','CommentController@index'); // all comments of a post
    Route::post('/posts/{id}/comments', 'CommentController@store'); // create comment on a post
    Route::put('/comments/{id}', 'CommentController@update'); // update a comment
    Route::delete('/comments/{id}', 'CommentController@destroy'); // delete a comment

    // Like
    Route::post('/posts/{id}/likes', 'LikeController@likeorunlike');

});

