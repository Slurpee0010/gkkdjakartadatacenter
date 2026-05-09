@extends('layouts.app')

@section('title', 'Master Buku PA')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span>Master Buku PA
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Master Buku PA</h1>
        <p class="page-subtitle">Kelola data referensi buku Pendalaman Alkitab</p>
    </div>
    <a href="{{ route('master_buku_pa.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Buku PA
    </a>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($bukuPas->count() > 0)
        <table class="gkkd-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Buku</th>
                    <th style="width: 120px;">Jumlah Bab</th>
                    <th style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bukuPas as $index => $buku)
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $buku->nama_buku }}</td>
                    <td><span class="gkkd-badge badge-info">{{ $buku->jumlah_bab }} Bab</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('master_buku_pa.edit', $buku->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                    data-url="{{ route('master_buku_pa.destroy', $buku->id) }}"
                                    data-name="{{ $buku->nama_buku }}">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <p>Belum ada data buku PA. Klik tombol "Tambah Buku PA" untuk menambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
