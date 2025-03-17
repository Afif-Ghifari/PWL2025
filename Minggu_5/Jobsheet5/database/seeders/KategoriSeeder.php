<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriData = [
            ['kategori_id' => 1, 'kategori_kode' => 'ELEC', 'kategori_nama' => 'Elektronik'],
            ['kategori_id' => 2, 'kategori_kode' => 'FASH', 'kategori_nama' => 'Fashion'],
            ['kategori_id' => 3, 'kategori_kode' => 'FOOD', 'kategori_nama' => 'Makanan & Minuman'],
            ['kategori_id' => 4, 'kategori_kode' => 'HOME', 'kategori_nama' => 'Peralatan Rumah'],
            ['kategori_id' => 5, 'kategori_kode' => 'ATK', 'kategori_nama' => 'Alat Kantor'],
        ];

        DB::table('m_kategori')->insert($kategoriData);
    }
}
