<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierData = [
            ['supplier_id' => 1, 'nama_supplier' => 'PT. Sumber Makmur', 'kontak' => '081234567890', 'alamat' => 'Jl. Merdeka No. 1, Jakarta'],
            ['supplier_id' => 2, 'nama_supplier' => 'CV. Berkah Jaya', 'kontak' => '082345678901', 'alamat' => 'Jl. Sudirman No. 23, Bandung'],
            ['supplier_id' => 3, 'nama_supplier' => 'UD. Sejahtera', 'kontak' => '083456789012', 'alamat' => 'Jl. Ahmad Yani No. 45, Surabaya'],
            ['supplier_id' => 4, 'nama_supplier' => 'PT. Maju Bersama', 'kontak' => '084567890123', 'alamat' => 'Jl. Diponegoro No. 67, Semarang'],
            ['supplier_id' => 5, 'nama_supplier' => 'Toko Sinar Terang', 'kontak' => '085678901234', 'alamat' => 'Jl. Gatot Subroto No. 89, Medan'],
        ];
        
        DB::table('m_supplier')->insert($supplierData);
    }
}
