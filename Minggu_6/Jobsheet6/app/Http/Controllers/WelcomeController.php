<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    
    /*public function hello()
    {
        return 'Hello World';
        public function greeting(){ 
            return view('blog.hello')
            ->with('name','Afif')
            ->with('occupation','Student'); 
        }
    }*/

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu='dashboard';
        return view('welcome',[
            'breadcrumb'=>$breadcrumb,
            'activeMenu'=>$activeMenu
        ]);
    }
}
