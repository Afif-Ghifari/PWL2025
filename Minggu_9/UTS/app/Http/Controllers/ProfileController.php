<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;


class ProfileController extends Controller
{
    public function show(string $id)
    {
        $user = UserModel::find($id);

        $breadcrumb = (object) [
            "title" => "Profil Pengguna",
            "list" => ['Home', 'Profil']
        ];

        $page = (object) [
            "title" => "Profil "
        ];

        $activeMenu = 'profile';

        return view('profile.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama' => 'required|string|max:100',
            'password' => 'nullable|min:5',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = UserModel::findOrFail($id);

        // Untuk upload file
        if ($request->hasFile('profile_pic')) { // cek jika field profile_pic terisi
            $image = $request->file('profile_pic'); // mengambil file gambar
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // mengambil dan mengubah nama file
            $image->move(public_path('img/profile_pic'), $filename); // memindahkan file ke folder
            
            $user->profile_pic = $filename; // mengubah field nilai profile_pic
        }
        // Update data yang lain
        $user->username = $request->username;
        $user->nama = $request->nama;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save(); // menyimpan perubahan data

        return redirect('/profile/'.$id.'/show')->with('success', 'Data user berhasil diubah');
    }
}
