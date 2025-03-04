<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
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

Route::get('/', function () {
    return view('welcome');
});

// Route untuk halaman kategori

// Route url ke halaman food-beverage
Route::get('/category/food-beverage', [CategoryController::class, 'foodBeverage']);

// Route url ke halaman beauty-health
Route::get('/category/beauty-health', [CategoryController::class, 'beautyHealth']);

// Route url ke halaman home-care
Route::get('/category/home-care', [CategoryController::class, 'homeCare']);

// Route url ke halaman baby-kid
Route::get('/category/baby-kid', [CategoryController::class, 'babyKid']);

// Route untuk halaman user
Route::get('/user/{id}/name/{name}' , [UserController::class, 'index']);

// Route untuk halaman sales
Route::get('/sales', function () {
    return view('sales');
});
