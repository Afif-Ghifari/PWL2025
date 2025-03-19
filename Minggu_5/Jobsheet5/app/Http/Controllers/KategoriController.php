<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\KategoriDataTable;
use App\Models\KategoriModel;

class KategoriController extends Controller
{
    public function index(KategoriDataTable $dataTable)
    {
        return $dataTable->render('kategori.index');
        
    }

    public function create(){
        return view('kategori.create');
    }

    public function store(Request $request){
        KategoriModel::create([
            'kategori_kode'=>$request->kategori_kode,
            'kategori_nama'=>$request->kategori_nama
        ]);
        return redirect('/kategori');
    }
}
