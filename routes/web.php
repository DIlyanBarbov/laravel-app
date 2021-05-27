<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('/', static function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
Route::post('/upload', [App\Http\Controllers\UserController::class, 'upload'])->name('upload');
Route::get('/upload', [App\Http\Controllers\UserController::class, 'upload'])->name('upload');
Route::get('/pictures', [App\Http\Controllers\UserController::class, 'pictures'])->name('pictures');
Route::post('/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
Route::get('/viewPictures', [App\Http\Controllers\UserController::class, 'viewPictures'])->name('viewPictures');
Route::post('/deletePicture', [App\Http\Controllers\UserController::class, 'deletePicture'])->name('deletePicture');
