@extends('layouts.app')

@section('title', 'Daftar Anak Bimbingan')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Anak Bimbingan
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Anak Bimbingan</h1>
        <p class="page-subtitle">Kelola data anak bimbingan gereja</p>
    </div>
    <a href="{{ route('anak_bimbingan.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Anak Bimbingan
    </a>
</div>

@include('partials.data-filters', [
    'title' => 'Filter Anak PA',
    'actionRoute' => 'anak_bimbingan.index',
    'exportRoute' => 'anak_bimbingan.export',
    'resetRoute' => 'anak_bimbingan.index',
    'filterIdPrefix' => 'anak_bimbingan',
    'wilayahParam' => 'wilayah_id',
    'pelayananParam' => 'pelayanan_id',
    'searchPlaceholder' => 'Anak PA, pembimbing, wilayah',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($anakBimbingans->count() > 0)
        <form method="POST" action="{{ route('anak_bimbingan.bulk-destroy') }}" class="bulk-delete-form" data-resource-label="Anak Bimbingan">
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
                            <input type="checkbox" class="form-check-input js-select-all" aria-label="Pilih semua anak bimbingan">
                        </th>
                        <th style="width: 60px;">No</th>
                        <th>Nama Anak</th>
                        <th>Pembimbing</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($anakBimbingans as $index => $anakBimbingan)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $anakBimbingan->id }}" class="form-check-input js-row-select" aria-label="Pilih {{ $anakBimbingan->nama_anak }}">
                        </td>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $anakBimbingan->nama_anak }}</td>
                        <td><span class="gkkd-badge badge-warning">{{ $anakBimbingan->pembimbing->nama_pembimbing ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-primary">{{ $anakBimbingan->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $anakBimbingan->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('anak_bimbingan.edit', $anakBimbingan->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('anak_bimbingan.destroy', $anakBimbingan->id) }}"
                                        data-name="{{ $anakBimbingan->nama_anak }}">
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
            <i class="fas fa-users"></i>
            <p>Tidak ada anak bimbingan sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection
