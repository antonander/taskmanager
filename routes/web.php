<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('taskmanager');
})->name('root');

Route::post('/', function () {
    return view('taskmanager');
})->name('root-post');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('taskmanager', 'TaskManagerController');

// Route::post('/getSearch', 'SearchController@getSearch')->name('post');


// Route::get('/js/', function () {
//     return view('taskmanager');
// });
