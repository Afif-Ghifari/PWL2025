@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-info mt-1 btn-sm">Import Supplier</button>
                <a class="btn btn-sm btn-success mt-1" href="{{ url('supplier/export_excel') }}">Export Supplier</a>
                <button onclick="modalAction('{{ url('supplier/create_ajax') }}')" class="btn btn-sm btn-primary mt-1">Tambah
                    Ajax</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Supplier</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        $(document).ready(function() {
            var dataSupplier = $('#table_supplier').DataTable({
                // serverSide: true, jika ingin menggunakan server side processing 
                serverSide: true,
                ajax: {
                    "url": "{{ url('supplier/list') }}",
                    "dataType": "json",
                    "type": "GET",
                },
                columns: [{
                    // nomor urut dari laravel datatable addIndexColumn() 
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }, {
                    data: "nama_supplier",
                    className: "",
                    // orderable: true, jika ingin kolom ini bisa diurutkan  
                    orderable: true,
                    // searchable: true, jika ingin kolom ini bisa dicari 
                    searchable: true
                }, {
                    data: "kontak",
                    className: "",
                    orderable: true,
                    searchable: true
                }, {
                    // mengambil data level hasil dari ORM berelasi 
                    data: "alamat",
                    className: "",
                    orderable: false,
                    searchable: false
                }, {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>
@endpush
