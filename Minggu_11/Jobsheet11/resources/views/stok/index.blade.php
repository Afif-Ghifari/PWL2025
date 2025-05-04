@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar stok</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info mt-1 btn-sm">Import stok</button>
                <a class="btn btn-sm btn-success mt-1" href="{{ url('stok/export_excel') }}">Export stok</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-sm mt-1 btn-warning"><i class="fa fa-file-pdf"></i>Export PDF stok</a>
                <button onclick="modalAction('{{ url('/stok/create') }}')" class="btn btn-sm btn-primary mt-1">Tambah
                    Ajax</button>
            </div>
        </div>
        <div class="card-body">
            <!-- untuk Filter data -->
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="barang_id" class="form-control form-control-sm" id="barang_id">
                                    <option value="">- Semua -</option>
                                    @foreach ($barang as $b)
                                        <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Filter stok barang</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Tiba</th>
                        <th>Penerima</th>
                        <th>Barang</th>
                        <th>Supplier</th>
                        <th>Jumlah Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var tableStok;
        $(document).ready(function() {
            tableStok = $('#table-stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('stok/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.barang_id = $(`#barang_id`).val();
                    }
                },
                columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    width: "5%",
                    orderable: false,
                    searchable: false
                }, 
                {
                    data: "stok_tanggal",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "user.username",
                    className: "",
                    width: "10%",
                    orderable: true,
                    searchable: true,
                }, 
                {
                    data: "barang.barang_nama",
                    className: "",
                    width: "14%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "supplier.nama_supplier",
                    className: "",
                    width: "14%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "stok_jumlah",
                    className: "",
                    width: "10%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "aksi",
                    className: "text-center",
                    width: "20%",
                    orderable: false,
                    searchable: false
                }]
            });
            $('#barang_id').on('change', function() {
                tableStok.ajax.reload();
            })
            // $('#table-stok_filter input').unbind().bind().on('keyup', function(e) {
            //     if (e.keyCode == 13) { // enter key 
            //         tableStok.search(this.value).draw();
            //     }
            // });

            // $('.barang_id').change(function() {
            //     tableStok.draw();
            // });
        });
    </script>
@endpush
