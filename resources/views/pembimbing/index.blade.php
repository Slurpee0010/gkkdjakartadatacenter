@extends('layouts.app')

@section('title', 'Daftar Pembimbing')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Pembimbing
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

@include('partials.data-filters', [
    'title' => 'Filter Pembimbing',
    'actionRoute' => 'pembimbing.index',
    'exportRoute' => 'pembimbing.export',
    'resetRoute' => 'pembimbing.index',
    'filterIdPrefix' => 'pembimbing',
    'wilayahParam' => 'wilayah_id',
    'pelayananParam' => 'pelayanan_id',
    'searchPlaceholder' => 'Pembimbing, wilayah, pelayanan',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($pembimbings->count() > 0)
        <form method="POST" action="{{ route('pembimbing.bulk-destroy') }}" class="bulk-delete-form" data-resource-label="Pembimbing">
            @csrf
            @method('DELETE')
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3" style="border-bottom: 1px solid var(--border);">
                <div style="color: var(--text-secondary); font-size: 0.85rem;">
                    <span class="bulk-selected-count">0</span> data dipilih
                </div>
                <button type="submit" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd js-bulk-delete-button" disabled>
                    <i class="fas fa-trash-alt"></i> Hapus Terpilih
                </button>
            </div>
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 48px;">
                            <input type="checkbox" class="form-check-input js-select-all" aria-label="Pilih semua pembimbing">
                        </th>
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
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $pembimbing->id }}" class="form-check-input js-row-select" aria-label="Pilih {{ $pembimbing->nama_pembimbing }}">
                        </td>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $pembimbing->nama_pembimbing }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $pembimbing->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $pembimbing->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pembimbing.edit', $pembimbing->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('pembimbing.destroy', $pembimbing->id) }}"
                                        data-name="{{ $pembimbing->nama_pembimbing }}">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </form>
        @else
        <div class="empty-state">
            <i class="fas fa-user-tie"></i>
            <p>Tidak ada pembimbing sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection
