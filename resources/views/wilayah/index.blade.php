@extends('layouts.app')

@section('title', 'Daftar Wilayah')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span>Wilayah
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Wilayah</h1>
        <p class="page-subtitle">Kelola data wilayah gereja</p>
    </div>
    <a href="{{ route('wilayah.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Wilayah
    </a>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($wilayahs->count() > 0)
        <table class="gkkd-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Wilayah</th>
                    <th style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wilayahs as $index => $wilayah)
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $wilayah->nama_wilayah }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('wilayah.edit', $wilayah->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('wilayah.destroy', $wilayah->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus wilayah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-map-marked-alt"></i>
            <p>Belum ada data wilayah. Klik tombol "Tambah Wilayah" untuk menambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
