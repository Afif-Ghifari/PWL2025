<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PhotoController; 
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
//     // return 'Selamat Datang';
// });
Route::get('/', [HomeController::class, 'index']);

Route::get('/hello', [WelcomeController::class, 'hello']);

Route::get('/world', function () { 
    return 'World'; 
});

Route::get('/about', [AboutController::class, 'about']);

// Route::get('/user/{name}', function ($name) { 
//     return 'Nama saya '.$name; 
// });

Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) { 
    return 'Pos ke-'.$postId." Komentar ke-: ".$commentId; 
});

Route::get('/articles/{id}', [ArticleController::class, 'articles']);

// Route::get('/user/{name?}', function ($name='John') {
//     return 'Nama saya '.$name; 
// });

Route::resource('photos', PhotoController::class);

Route::resource('photos', PhotoController::class)->only([ 
    'index', 'show' 
]); 
Route::resource('photos', PhotoController::class)->except([ 
    'create', 'store', 'update', 'destroy' 
]);

Route::get('/greeting',  [WelcomeController::class, 
'greeting']); 

//----------JS3-----------//

Route::get('/level', [LevelController::class, 'index']);
Route::get('/kategori', [KategoriController::class, 'index']);

//----------JS4-----------//


//----------JS5-----------//
Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix'=> 'user'], function(){
    Route::get('/', [UserController::class, 'index']);
    Route::post('/list', [UserController::class, 'list']);
    Route::get('/create', [UserController::class, 'create']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/edit', [UserController::class, 'edit']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});