<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;

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

        $pj = "PJ005";
        // $data = 'tidak ada';
        // if (PenjualanModel::where('penjualan_kode', $pj)->exists()) {
        //     $data = 'ada';
        // }
        $data = PenjualanModel::select('penjualan_id')->where('penjualan_kode', '=', $pj)->value('penjualan_id');

        $activeMenu='dashboard';
        return view('welcome',[
            'breadcrumb'=>$breadcrumb,
            'activeMenu'=>$activeMenu,
            // 'data'=>$data
        ]);
    }
}
