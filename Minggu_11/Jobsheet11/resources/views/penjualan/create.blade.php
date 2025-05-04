@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('penjualan/store') }}" class="form-horizontal" id="form-penjualan">
                @csrf

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Pegawai Menangani</label>
                    <div class="col-sm-10">
                        <select name="user_id" class="form-control">
                            @foreach ($user as $u)
                                <option value="{{ $u->user_id }}">{{ $u->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Kode Penjualan</label>
                    <div class="col-sm-10">
                        <input type="text" name="penjualan_kode" class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Pembeli</label>
                    <div class="col-sm-10">
                        <input type="text" name="pembeli" class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Tanggal Penjualan</label>
                    <div class="col-sm-10">
                        <input type="date" name="penjualan_tanggal" class="form-control">
                    </div>
                </div>

                <hr>

                <h5>Produk Belanja</h5>
                <table class="table table-bordered" id="table-detail">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga Jual</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-body">
                        <!-- Rows will be added here -->
                    </tbody>
                </table>

                <button type="button" class="btn btn-sm btn-success" id="btn-tambah-row">+ Tambah Barang</button>
                <br><br>

                <a href="{{ url('penjualan') }}" class="btn btn-warning">Kembali</a>
                <button type="submit" class="btn btn-primary mx-3">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@push('css')
<style>
    .invalid-feedback {
        display: block;
    }
</style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        let barangOptions = @json($barang); // Misalnya $barang adalah array of {id, nama, harga_jual}

        function createRow() {
            let options = '';
            barangOptions.forEach(barang => {
                options +=
                    `<option value="${barang.barang_id}" data-harga="${barang.harga_jual}">${barang.barang_nama}</option>`;
            });

            return `
            <tr>
                <td>
                    <select name="barang_id[]" class="form-control select-barang">
                        <option value="">-- Pilih Barang --</option>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="text" name="harga_jual[]" class="form-control harga-jual" readonly>
                </td>
                <td>
                    <input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1">
                </td>
                <td>
                    <input type="text" class="form-control subtotal" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-row">Hapus</button>
                </td>
            </tr>
        `;
        }

        function hitungSubtotal(row) {
            let harga = parseFloat(row.find('.harga-jual').val()) || 0;
            let jumlah = parseFloat(row.find('.jumlah').val()) || 0;
            row.find('.subtotal').val(harga * jumlah);
        }

        $(document).ready(function() {
            $('#btn-tambah-row').on('click', function() {
                $('#detail-body').append(createRow());
            });

            // Tambah baris pertama otomatis
            $('#btn-tambah-row').click();

            $('#detail-body').on('change', '.select-barang', function() {
                let selected = $(this).find(':selected');
                let harga = selected.data('harga');
                let row = $(this).closest('tr');
                row.find('.harga-jual').val(harga);
                hitungSubtotal(row);
            });

            $('#detail-body').on('input', '.jumlah', function() {
                let row = $(this).closest('tr');
                hitungSubtotal(row);
            });

            $('#detail-body').on('click', '.btn-hapus-row', function() {
                $(this).closest('tr').remove();
            });

            // Validasi form
        $('#form-penjualan').validate({
            ignore: [],
            rules: {
                user_id: "required",
                penjualan_kode: "required",
                pembeli: "required",
                penjualan_tanggal: "required",
                'barang_id[]': {
                    required: true
                },
                'jumlah[]': {
                    required: true,
                    min: 1
                }
            },
            messages: {
                user_id: "Pilih user.",
                penjualan_kode: "Isi kode penjualan.",
                pembeli: "Isi nama pembeli.",
                penjualan_tanggal: "Pilih tanggal.",
                'barang_id[]': "Pilih barang.",
                'jumlah[]': {
                    required: "Jumlah wajib diisi.",
                    min: "Jumlah minimal 1."
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.closest('.form-group').length) {
                    element.closest('.form-group').append(error);
                } else {
                    element.closest('td').append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        });
    </script>
@endpush
