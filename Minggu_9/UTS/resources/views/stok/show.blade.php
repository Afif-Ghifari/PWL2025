@empty($stok)
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
                <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <table class="table table-striped">
                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $stok->barang->barang_nama }}</td>
                </tr>
                <tr>
                    <th>Kategori Barang</th>
                    <td>{{ $stok->barang->kategori->kategori_nama }}</td>
                </tr>
                <tr>
                    <th>Nama user</th>
                    <td>{{ $stok->user->username }}</td>
                </tr>
                <tr>
                    <th>Nama Supplier</th>
                    <td>{{ $stok->supplier->nama_supplier }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pengiriman</th>
                    <td>{{ $stok->stok_tanggal }}</td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td>{{ $stok->stok_jumlah }}</td>
                </tr>
            </table>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
            </div>
        </div>
    </div>
@endempty
