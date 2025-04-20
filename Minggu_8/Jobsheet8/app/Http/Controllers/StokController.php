<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\stokModel;
use App\Models\barangModel;
use App\Models\UserModel;
use App\Models\SupplierModel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;  
use Barryvdh\DomPDF\Facade\Pdf;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object)[
            'title' => 'Daftar stok',
        ];

        $activeMenu = 'stok';
        // $stok = StokModel::all();
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();
        return view('stok.index', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'barang' => $barang,
            'supplier' => $supplier,
            'user' => $user,
            'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select(
            'barang_id',
            'user_id',
            'supplier_id',
            'stok_tanggal',
            'stok_jumlah'
        )
        ->with([
            'barang',
            'user',
            'supplier'
        ]);

        if ($request->barang_id) {
            $stoks->where('barang_id', $request->barang_id);
        }
        if ($request->user_id) {
            $stoks->where('user_id', $request->user_id);
        }
        if ($request->supplier_id) {
            $stoks->where('supplier_id', $request->supplier_id);
        }
        return DataTables::of($stoks)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {  // menambahkan kolom aksi 
                $btn  = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id .
                    '/show') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id .
                    '/edit') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id .
                    '/delete') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
