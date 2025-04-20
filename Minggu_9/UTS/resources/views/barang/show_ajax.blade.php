@empty($barang)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <table class="table table-striped">
                <tr>
                    <th>Kode barang</th>
                    <td>{{ $barang->barang_kode }}</td>
                </tr>
                <tr>
                    <th>Nama barang</th>
                    <td>{{ $barang->barang_nama }}</td>
                </tr>
                <tr>
                    <th>Harga beli</th>
                    <td>{{ $barang->harga_beli }}</td>
                </tr>
                <tr>
                    <th>Harga jual</th>
                    <td>{{ $barang->harga_jual }}</td>
                </tr>
                <tr>
                    <th>Nama barang</th>
                    <td>{{ $barang->kategori->kategori_nama }}</td> 
                </tr>
            </table>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
            </div>
        </div>
    </div>
@endempty
