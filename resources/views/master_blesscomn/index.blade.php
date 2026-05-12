@extends('layouts.app')

@section('title', 'Daftar Master Blesscomn')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Master Blesscomn
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Master Blesscomn</h1>
        <p class="page-subtitle">Kelola data blesscomn gereja</p>
    </div>
    <a href="{{ route('master_blesscomn.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Blesscomn
    </a>
</div>

@include('partials.data-filters', [
    'title' => 'Filter Master Blesscomn',
    'actionRoute' => 'master_blesscomn.index',
    'exportRoute' => 'master_blesscomn.export',
    'resetRoute' => 'master_blesscomn.index',
    'filterIdPrefix' => 'master_blesscomn',
    'wilayahParam' => 'id_wilayah',
    'pelayananParam' => 'id_pelayanan',
    'searchPlaceholder' => 'Blesscomn, ketua, asisten',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($blesscomns->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Blesscomn</th>
                        <th>Tgl Terbentuk</th>
                        <th>Ketua</th>
                        <th>Asisten</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th>Pembelahan</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blesscomns as $index => $item)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $item->nama_blesscomn }}</td>
                        <td>{{ $item->tanggal_terbentuk->format('d M Y') }}</td>
                        <td>{{ $item->pengurus->nama_ketua ?? '-' }}</td>
                        <td>{{ $item->pengurus->nama_asisten ?? '-' }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $item->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $item->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td>
                            @if($item->is_pembelahan)
                                <span class="gkkd-badge badge-warning">
                                    <i class="fas fa-code-branch me-1"></i> {{ $item->blesscomnInduk->nama_blesscomn ?? '-' }}
                                </span>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('master_blesscomn.edit', $item->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('master_blesscomn.destroy', $item->id) }}"
                                        data-name="{{ $item->nama_blesscomn }}">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-church"></i>
            <p>Tidak ada blesscomn sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection
