@extends('layouts.app')

@section('title', 'Daftar Pembimbing')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span>Pembimbing
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Pembimbing</h1>
        <p class="page-subtitle">Kelola data pembimbing gereja</p>
    </div>
    <a href="{{ route('pembimbing.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Pembimbing
    </a>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($pembimbings->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Pembimbing</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembimbings as $index => $pembimbing)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $pembimbing->nama_pembimbing }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $pembimbing->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $pembimbing->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pembimbing.edit', $pembimbing->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('pembimbing.destroy', $pembimbing->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pembimbing ini?')">
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
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-user-tie"></i>
            <p>Belum ada data pembimbing. Klik tombol "Tambah Pembimbing" untuk menambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
