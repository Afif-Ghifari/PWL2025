<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fungsi yang menampilkan view halaman user dan mengirimkan data id dan name
    public function index($id, $name){
        return view('user', [
            'id' => $id,
            'name' => $name
        ]);
    }
}
