<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: 80px;
            height: 80px;
            /* max-width: 150px;
            max-height: 150px; */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
        #header{
            text-align: left;
            font-size: 1em;
            line-height: 0.8;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            {{-- <td width="15%" class="text-center"><img src="{{ public_path('img/polinema-bw.png') }}"></td> --}}
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN
                    PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI
                    MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang
                    65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101
                    105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA STOK BARANG</h4>
        <div id="header">
            <p>Kode Penjualan: {{ $penjualan->penjualan_kode }}</p>
            <p>Pegawai: {{ $penjualan->user->username }}</p>
            <p>Pembeli: {{ $penjualan->pembeli }}</p>
            <p>Tanggal Penjualan: {{ $penjualan->penjualan_tanggal }}</p>
        </div>
        <table class="border-all">
            <thead>
                <tr>
                    <th class="text-center">No</th>
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
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $d->barang->barang_nama ?? '-' }}</td>
                            <td>Rp. {{ number_format($d->barang->harga_jual)}}</td>
                            <td>{{ $d->jumlah }}</td>
                            <td>Rp. {{ number_format($subtotal) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">Total</td>
                        <td><strong>Rp. {{ number_format($grandTotal) }}</strong></td>
                    </tr>
            </tbody>
        </table>
</body>

</html>
