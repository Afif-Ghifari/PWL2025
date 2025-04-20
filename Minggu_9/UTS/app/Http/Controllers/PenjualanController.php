<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\PenjualanDetailModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object)[
            'title' => 'Daftar Penjualan',
        ];

        $activeMenu = 'penjualan';
        $user = UserModel::all();
        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select(
            'penjualan_id',
            'user_id',
            'pembeli',
            'penjualan_kode',
            'penjualan_tanggal'
        )->with('user');

        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }
        return DataTables::of($penjualans)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {  // menambahkan kolom aksi 
                $btn  = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
                    '/show') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<a href="' . url('/penjualan/' . $penjualan->penjualan_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
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
        $breadcrumb = (object)[
            'title' => 'Tambah Penjualan',
            'list' => ['Home', 'Penjualan', 'Tambah']
        ];
        $page = (object)[
            'title' => 'Tambah Penjualan Baru',
        ];

        $user = UserModel::all();
        $barang = BarangModel::all();
        $activeMenu = 'penjualan';
        return view('penjualan.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'penjualan_kode' => 'required|unique:t_penjualan,penjualan_kode',
            'pembeli' => 'required|string',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga_jual' => 'required|array',
            'harga_jual.*' => 'required|numeric',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Simpan ke tabel penjualan
            $penjualan = PenjualanModel::create([
                'user_id' => $request->user_id,
                'penjualan_kode' => $request->penjualan_kode,
                'pembeli' => $request->pembeli,
                'penjualan_tanggal' => $request->penjualan_tanggal,
            ]);

            // Loop setiap detail
            foreach ($request->barang_id as $index => $barangId) {
                $jumlah = $request->jumlah[$index];
                $harga = $request->harga_jual[$index];
                $subtotal = $jumlah * $harga;

                // Kurangi stok
                $stok = StokModel::where('barang_id', $barangId)->first();
                if (!$stok || $stok->stok_jumlah < $jumlah) {
                    throw new \Exception("Stok barang tidak mencukupi untuk barang ID: $barangId");
                }

                $stok->stok_jumlah -= $jumlah;
                $stok->save();

                // Simpan detail penjualan
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barangId,
                    'harga' => $subtotal,
                    'jumlah' => $jumlah,
                ]);
            }

            DB::commit();
            return redirect('/penjualan')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        $penjualanDetail = PenjualanDetailModel::with('barang')
            ->where('penjualan_id', $id)
            ->get();

        $user = UserModel::select('user_id', 'username')->get();

        return view('penjualan.show', [
            'penjualan' => $penjualan,
            'penjualanDetail' => $penjualanDetail,
            'user' => $user,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumb = (object)[
            'title' => 'Edit Penjualan',
            'list' => ['Home', 'Penjualan', 'Edit']
        ];
        $page = (object)[
            'title' => 'Edit Penjualan Baru',
        ];
        $penjualan = PenjualanModel::with('detail')->find($id);
        $user = UserModel::all();
        $barang = BarangModel::all();
        $activeMenu = 'penjualan';
        return view('penjualan.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'penjualan' => $penjualan,
            'user' => $user,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data utama dan detail
        $request->validate([
            'user_id' => 'required|integer',
            'penjualan_kode' => 'required|string',
            'pembeli' => 'required|string',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:m_barang,barang_id',
            'harga_jual' => 'required|array',
            'harga_jual.*' => 'required|numeric',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Update data penjualan utama
            $penjualan = PenjualanModel::findOrFail($id);
            $penjualan->update([
                'user_id' => $request->user_id,
                'penjualan_kode' => $request->penjualan_kode,
                'pembeli' => $request->pembeli,
                'penjualan_tanggal' => $request->penjualan_tanggal,
            ]);

            // Hapus detail sebelumnya & kembalikan stok-nya
            foreach ($penjualan->detail as $detail) {
                // Tambahkan kembali stok
                $stok = StokModel::where('barang_id', $detail->barang_id)->first();
                if ($stok) {
                    $stok->stok_jumlah += $detail->jumlah;
                    $stok->save();
                }
                $detail->delete();
            }

            // Simpan detail baru & kurangi stok
            foreach ($request->barang_id as $i => $barang_id) {
                $jumlah = $request->jumlah[$i];
                $harga_satuan = $request->harga_jual[$i];
                $subtotal = $jumlah * $harga_satuan;

                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_id,
                    'jumlah' => $jumlah,
                    'harga' => $subtotal,
                ]);

                // Kurangi stok
                $stok = StokModel::where('barang_id', $barang_id)->first();
                if ($stok) {
                    if ($stok->stok_jumlah < $jumlah) {
                        throw new \Exception("Stok barang ID $barang_id tidak mencukupi.");
                    }

                    $stok->stok_jumlah -= $jumlah;
                    $stok->save();
                }
            }

            DB::commit();
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal update penjualan: ' . $e->getMessage());
        }
    }


    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        $penjualanDetail = PenjualanDetailModel::with('barang')
            ->where('penjualan_id', $id)
            ->get();

        $user = UserModel::select('user_id', 'username')->get();

        return view('penjualan.confirm_ajax', [
            'penjualan' => $penjualan,
            'penjualanDetail' => $penjualanDetail,
            'user' => $user,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);
            if ($penjualan) {
                $penjualan->delete();
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
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 2MB 
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:2048']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_penjualan');  // ambil file dari request 

            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 

            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 


            $insertPenjualan = [];
            $insertPenjualanDetail = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati 
                        // $insertPenjualan[] = [
                        //     'user_id' => $value['D'],
                        //     'pembeli' => $value['C'],
                        //     'penjualan_kode' => $value['A'],
                        //     'penjualan_tanggal' => $value['B'],
                        //     'created_at' => now(),
                        // ];

                        // variabel penjualan & penjualan detail
                        $kode = $value['A'];
                        $tanggal = $value['B'];
                        $pembeli = $value['C'];
                        $user_id = $value['D'];
                        $barang_id = $value['E'];
                        $jumlah = $value['F'];
                        $harga = $value['G'];

                        $tempKode = $kode;

                        // cek apakah penjualan dengan kode penjualan yang sama sudah ada
                        if (!PenjualanModel::where('penjualan_kode', $tempKode)->exists()) {

                            // jika tidak ada membuat penjualan baru
                            $insertPenjualan[] = [
                                'user_id' => $user_id,
                                'pembeli' => $pembeli,
                                'penjualan_kode' => $kode,
                                'penjualan_tanggal' => $tanggal,
                                'created_at' => now(),
                            ];

                            PenjualanModel::insertOrIgnore($insertPenjualan);
                        }

                        // mengambil id penjualan
                        $penjualan_id = PenjualanModel::select('penjualan_id')->where('penjualan_kode', '=', $tempKode)->value('penjualan_id');

                        // insert penjualan detail
                        $insertPenjualanDetail[] = [
                            'penjualan_id' => $penjualan_id,
                            'barang_id' => $barang_id,
                            'jumlah' => $jumlah,
                            'harga' => $harga,
                            'created_at' => now(),
                        ];

                        PenjualanDetailModel::insertOrIgnore($insertPenjualanDetail);

                        // reset variabel
                        $insertPenjualan = [];
                        $insertPenjualanDetail = [];
                    }
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

    public function export_pdf()
    {
        $penjualan = PenjualanModel::select('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with([
                'user'
            ])
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function export_excel()
    {
        // Ambil data barang yang akan dieksport
        $penjualan = PenjualanModel::select('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with([
                'user',
                'detail.barang',
            ])
            ->get();

        $penjualanDetail = PenjualanDetailModel::with(['barang', 'barang.kategori', 'penjualan'])->get();


        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'Kode Penjualan');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'Pegawai Menangani');
        $sheet->setCellValue('F1', 'Nama Barang');
        $sheet->setCellValue('G1', 'Kategori Barang');
        $sheet->setCellValue('H1', 'Harga Satuan');
        $sheet->setCellValue('I1', 'Jumlah');
        $sheet->setCellValue('J1', 'SubTotal');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);  // bold header

        $no = 1;                  // nomor data dimulai dari 1
        $baris = 2;               // baris data dimulai dari baris ke 2
        foreach ($penjualanDetail as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->penjualan->penjualan_tanggal);
            $sheet->setCellValue('C' . $baris, $value->penjualan->penjualan_kode);
            $sheet->setCellValue('D' . $baris, $value->penjualan->pembeli);
            $sheet->setCellValue('E' . $baris, $value->penjualan->user->username);
            $sheet->setCellValue('F' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('G' . $baris, $value->barang->kategori->kategori_nama);
            $sheet->setCellValue('H' . $baris, $value->barang->harga_jual);
            $sheet->setCellValue('I' . $baris, $value->jumlah);
            $sheet->setCellValue('J' . $baris, $value->jumlah * $value->barang->harga_jual);
            $baris++;
            $no++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data penjualan'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

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

    public function export_detail_pdf(string $id)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with([
                'user',
                'detail.barang',
            ])
            ->where('penjualan_id', $id)
            ->firstOrFail();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('penjualan.export_detail_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
