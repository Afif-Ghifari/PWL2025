<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\stokModel;
use App\Models\BarangModel;
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
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select(
            'stok_id',
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
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'username')->get();
        $supplier = SupplierModel::select('supplier_id', 'nama_supplier')->get();
        return view('stok.create', [
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier
        ]);
        // ->with('barang', $barang);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            StokModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->with(['kategori:id,kategori_id,kategori_nama'])->get();
        $user = UserModel::select('user_id', 'username')->get();
        $supplier = SupplierModel::select('supplier_id', 'nama_supplier')->get();
        return view('stok.show', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->with(['kategori:id,kategori_id,kategori_nama'])->get();
        $user = UserModel::select('user_id', 'username')->get();
        $supplier = SupplierModel::select('supplier_id', 'nama_supplier')->get();
        return view('stok.edit', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                'barang_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = StokModel::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
            return redirect('/')->with('success', 'Data penjualan berhasil diperbarui.');
        }catch(\Exception $e){
            return redirect('/')->with('error', 'Data gagal diupdate');
        }
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->with(['kategori:id,kategori_id,kategori_nama'])->get();
        $user = UserModel::select('user_id', 'username')->get();
        $supplier = SupplierModel::select('supplier_id', 'nama_supplier')->get();
        return view('stok.confirm_ajax', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            if ($stok) {
                $stok->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB 
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');  // ambil file dari request 

            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 

            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 

            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati 
                        $insert[] = [
                            'barang_id' => $value['A'],
                            'user_id' => $value['B'],
                            'supplier_id' => $value['C'],
                            'stok_tanggal' => $value['D'],
                            'stok_jumlah' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    StokModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }
    public function export_excel()
    {
        // Ambil data barang yang akan dieksport
        $stok = StokModel::select('barang_id', 'user_id', 'supplier_id', 'stok_tanggal', 'stok_jumlah')
            ->with([
                'barang.kategori',
                'user',
                'supplier'
            ])
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Kategori Barang');
        $sheet->setCellValue('D1', 'Nama Penerima');
        $sheet->setCellValue('E1', 'Supplier');
        $sheet->setCellValue('F1', 'Tanggal Terima');
        $sheet->setCellValue('G1', 'Jumlah');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);  // bold header

        $no = 1;                  // nomor data dimulai dari 1
        $baris = 2;               // baris data dimulai dari baris ke 2
        foreach ($stok as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('C' . $baris, $value->barang->kategori->kategori_nama);
            $sheet->setCellValue('D' . $baris, $value->user->username);
            $sheet->setCellValue('E' . $baris, $value->supplier->nama_supplier);
            $sheet->setCellValue('F' . $baris, $value->stok_tanggal);
            $sheet->setCellValue('G' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data Stok'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = StokModel::select('barang_id', 'user_id', 'supplier_id', 'stok_tanggal', 'stok_jumlah')
            ->with([
                'barang.kategori',
                'user',
                'supplier'
            ])
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
