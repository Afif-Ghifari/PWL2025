<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        return 'Selamat Datang';
    }

    public function about(){
        return 'Muhammad Afif Al Ghifari, 2341720168';
    }

    public function articles($id){
        return 'Halaman Artikel dengan Id '.$id;
    }
}
