<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;


class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];

        $page = (object)[
            'title' => 'Daftar User',
        ];

        $activeMenu = 'user';
        $level = LevelModel::all();
        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }
    public function create()
    {
        $breadcrumb = (object)[
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];
        $page = (object)[
            'title' => 'Tambah User Baru',
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';
        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:5',
                'level_id' => 'required|integer'
            ]);

            UserModel::create([
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => bcrypt($request->password),
                'level_id' => $request->level_id
            ]);
            return redirect('/user')->with('success', 'Data user berhasil disimpan');
        } catch (\Exception $e) {
            return redirect('/user')->with('error', 'Data user gagal disimpan' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail user'
        ];

        $activeMenu = 'user'; // Set menu yang sedang aktif

        return view('user.show', [
            'user' => $user,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object) [
            "title" => "Edit User",
            "list" => ['Home', 'User', 'Edit']
        ];

        $page = (object) [
            "title" => "Edit user"
        ];

        $activeMenu = 'user';

        return view('user.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'level' => $level,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama' => 'required|string|max:100',
            'password' => 'nullable|min:5',
            'level_id' => 'required|integer'
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }


    public function destroy(string $id)
    {
        $check = UserModel::find($id);
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }
        try {
            Usermodel::destroy($id);
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'Data user sedang digunakan');
        }
    }

    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');

        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }
        return DataTables::of($users)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {  // menambahkan kolom aksi 
                $btn  = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id .
                    '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true);
    }

    public function create_ajax()
    {
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax')
            ->with('level', $level);
    }

    public function store_ajax(Request $request)
    {
        try {

            // cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'level_id'  => 'required|integer',
                    'username'  => 'required|string|min:3|unique:m_user,username',
                    'nama'      => 'required|string|max:100',
                    'password'  => 'required|min:6'
                ];

                // use Illuminate\Support\Facades\Validator;
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, // response status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors(), // pesan error validasi
                    ]);
                }

                UserModel::create($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data user berhasil disimpan'
                ]);
            }
            return redirect('/user')->with('success', 'Data user berhasil disimpan');
        } catch (\Exception $e) {
            return redirect('/user')->with('error', 'Data user gagal disimpan' . $e->getMessage());
        }
    }


    public function show_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view(
            'user.show_ajax',
            [
                'user' => $user,
                'level' => $level
            ]
        );
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view(
            'user.edit_ajax',
            [
                'user' => $user,
                'level' => $level
            ]
        );
    }

    public function update_ajax(Request $request, $id)
    {
        try {
            // cek apakah request dari ajax 
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'level_id' => 'required|integer',
                    'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                    'nama'     => 'required|max:100',
                    'password' => 'nullable|min:6|max:20',
                ];
                // use Illuminate\Support\Facades\Validator; 
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status'   => false,    // respon json, true: berhasil, false: gagal 
                        'message'  => 'Validasi gagal.',
                        'msgField' => $validator->errors()  // menunjukkan field mana yang error 
                    ]);
                }

                $check = UserModel::find($id);
                if ($check) {
                    if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request 
                        $request->request->remove('password');
                    }

                    $check->update($request->all());
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil diupdate'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            }
            return redirect('/user')->with('success', 'Data user berhasil diupdate');
        } catch (\Exception $e) {
            return redirect('/user')->with('error', 'Data user gagal diupdate' . $e->getMessage());
        }
    }

    public function confirm_ajax(string $id)
    {
        $user = UserModel::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $user = UserModel::find($id);
                if ($user) {
                    $user->delete();
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
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Exception $e) {
            return redirect('/user')->with('error', 'Data user gagal dihapus' . $e->getMessage());
        }
    }

    public function import()
    {
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    // validasi file harus xls atau xlsx, max 1MB 
                    'file_user' => ['required', 'mimes:xlsx', 'max:1024']
                ];

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors()
                    ]);
                }

                $file = $request->file('file_user');  // ambil file dari request 

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
                                'level_id' => $value['A'],
                                'username' => $value['B'],
                                'nama' => $value['C'],
                                'password' => $value['D'],
                                'created_at' => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        // insert data ke database, jika data sudah ada, maka diabaikan 
                        UserModel::insertOrIgnore($insert);
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
            return redirect('/')->with('success', 'Data user berhasil disimpan');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Data user gagal disimpan' . $e->getMessage());
        }
    }

    public function export_excel()
    {
        // Ambil data yang akan dieksport
        $user = UserModel::select('level_id', 'username', 'nama', 'password')
            ->orderBy('level_id')
            ->with('level')
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama user');
        $sheet->setCellValue('D1', 'Password');
        $sheet->setCellValue('E1', 'level');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);  // bold header

        $no = 1;                  // nomor data dimulai dari 1
        $baris = 2;               // baris data dimulai dari baris ke 2
        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->username);
            $sheet->setCellValue('C' . $baris, $value->nama);
            $sheet->setCellValue('D' . $baris, $value->password);
            $sheet->setCellValue('E' . $baris, $value->level->level_nama); // ambil nama level
            $baris++;
            $no++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data user'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data user ' . date('Y-m-d H:i:s') . '.xlsx';

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
        $user = UserModel::select('level_id', 'username', 'nama')
            ->orderBy('level_id')
            ->with('level')
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('user.export_pdf', ['user' => $user]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data user ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
