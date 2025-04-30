<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'level_id',
        'username',
        'nama',
        'password',
        'profile_pic', // menambahkan field baru
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['password']; // tidak tampil saat di select
    protected $casts= ['password' => 'hashed']; // casting password ke hash

    public function level():BelongsTo{
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    // Mendapatkan nama role
    public function getRoleName():string{
        return $this->level->level_name;
    }

    // cek apakah user memiliki role tertentu
    public function hasRole($role):bool{
        return $this->level->level_kode == $role;
    }

    // mendapatkan kode role
    public function getRole(){
        return $this->level->level_kode;
    }
}
