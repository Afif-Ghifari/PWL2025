@extends('layout.app')

{{-- Customize layout sections --}}
@section('subtitle', 'Kategori')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Manage Kategori</div>
            <div class="card-body">
                {{-- Tombol yang mengarahkan ke halaman tambah data kategori --}}
                <button onclick="window.location.href = '/kategori/create'" class="btn btn-success my-3">
                    {{-- Icon tambah (plus) --}}
                    <i class="fas fa-plus"></i> 
                    Tambah
                </button>
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
