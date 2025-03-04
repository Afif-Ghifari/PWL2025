<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    /**
     * Menampilkan halaman utama untuk stock.
     */
    public function index()
    {
        //mengambil semua data stock dan menampilkan dalam halaman
        $stocks = Stock::all();
        return view('stocks.index', compact('stocks'));
    }

    /**
     * Mengarahkan ke halaman untuk membuat stock.
     */
    public function create()
    {
        return view('stocks.create');
    }

    /**
     * Menyimpan data stock baru ke dalam database
     */
    public function store(Request $request)
    {
        //melakukan validasi data yang dikirim
        $request->validate([
            'name' => 'required',
            'qty' => 'required',
        ]);

        //menyimpan data yang telah di validasi ke dalam database
        Stock::create($request->only(['name', 'qty']));
        return redirect()->route('stocks.index')->with('success', 'Stock added successfully.');
    }

    /**
     * mengambil data stock berdasarkan ID dari database
     */
    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    /**
     * Mengarahkan ke halaman untuk mengedit data stock
     */
    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    /**
     * Memperbarui data stock berdasarkan ID
     */
    public function update(Request $request, Stock $stock)
    {
        //melakukan validasi data yang dikirim
        $request->validate([
            'name' => 'required',
            'qty' => 'required',
        ]);

        //memperbarui dan mengirim data yang telah di validasi ke dalam database
        $stock->update($request->only(['name', 'qty']));
        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //menghapus data stock berdasarkan ID
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
