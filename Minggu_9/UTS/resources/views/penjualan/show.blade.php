@empty($penjualan)
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
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content px-5 py-2">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="my-1">
                <h5>Kode Penjualan: {{ $penjualan->penjualan_kode }}</h5>
                <h6>Pegawai: {{ $penjualan->user->username }}</h6>
                <h6>Pembeli: {{ $penjualan->pembeli }}</h6>
                <h6>Tanggal Penjualan: {{ $penjualan->penjualan_tanggal }}</h6>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach ($penjualan->detail as $d)
                        @php
                            $subtotal = $d->barang->harga_jual * $d->jumlah;
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $d->barang->barang_nama ?? '-' }}</td>
                            <td>Rp. {{ number_format($d->barang->harga_jual)}}</td>
                            <td>{{ $d->jumlah }}</td>
                            <td>Rp. {{ number_format($subtotal) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">Total</td>
                        <td><strong>Rp. {{ number_format($grandTotal) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            <div class="modal-footer justify-between">
                <a href="{{ url('/penjualan/'. $penjualan->penjualan_id .'/export_detail_pdf') }}" class="btn mt-1 btn-info"><i class="fa fa-file-pdf"></i>Export PDF detail penjualan</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning">Kembali</button>
            </div>
        </div>
    </div>
@endempty
