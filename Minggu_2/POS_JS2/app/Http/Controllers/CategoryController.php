<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Fungsi yang menampilkan view halaman kategori
    public function foodBeverage(){
        return view('category/food-beverage');
    }

    // Fungsi yang menampilkan view halaman kategori
    public function beautyHealth(){
        return view('category/beauty-health');
    }

    // Fungsi yang menampilkan view halaman kategori
    public function homeCare(){
        return view('category/home-care');
    }

    // Fungsi yang menampilkan view halaman kategori
    public function babyKid(){
        return view('category/baby-kid');
    }
}
