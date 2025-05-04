@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penjualan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info mt-1 btn-sm">Import penjualan</button>
                <a class="btn btn-sm btn-success mt-1" href="{{ url('penjualan/export_excel') }}">Export penjualan</a>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-sm mt-1 btn-warning"><i class="fa fa-file-pdf"></i>Export PDF penjualan</a>
                <a href="/penjualan/create" class="btn btn-sm btn-primary mt-1">+ Tambah Penjualan</a>
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
                                <select name="user_id" class="form-control form-control-sm" id="user_id">
                                    <option value="">- Semua -</option>
                                    @foreach ($user as $b)
                                        <option value="{{ $b->user_id }}">{{ $b->username }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Filter penjualan user</small>
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
            <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Penjualan</th>
                        <th>Kode Penjualan</th>
                        <th>Petugas Menangani</th>
                        <th>Pembeli</th>
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

        var tablePenjualan;
        $(document).ready(function() {
            tablePenjualan = $('#table-penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.user_id = $(`#user_id`).val();
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
                    data: "penjualan_tanggal",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "penjualan_kode",
                    className: "",
                    width: "10%",
                    orderable: true,
                    searchable: true,
                }, 
                {
                    data: "user.username",
                    className: "",
                    width: "14%",
                    orderable: true,
                    searchable: true
                }, 
                {
                    data: "pembeli",
                    className: "",
                    width: "14%",
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
            $('#user_id').on('change', function() {
                tablePenjualan.ajax.reload();
            })
        });
    </script>
@endpush
