<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $user = UserModel::where('username', 'manager9')->firstOrFail();
        return view('user', ['data' => $user]);
        
        // $user = UserModel::findOrFail(1);
        // $user = UserModel::findOr(20, ['username', 'password'], 
        //     function() { abort(404); 
        // });
        // $user = UserModel::firstWhere('level_id', 1);
        // $user = UserModel::where('level_id', 1)->first();
        // $user = UserModel::find(1);
        // $data = [
        //     'level_id' => 2,
        //     'username' => 'manager_tiga',
        //     'nama' => 'Manager 3',
        //     'password' => Hash::make('12345'),
        // ];
        // UserModel::create($data);

        // $user = UserModel::all();
        // $data = [
        //     'nama' => 'Pelanggan pertama'
        // ];
        // UserModel::where('username', 'customer-1')->update($data);

    }
}
