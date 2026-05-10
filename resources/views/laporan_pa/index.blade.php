@extends('layouts.app')

@section('title', 'Laporan PA')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span>Laporan PA
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Laporan PA</h1>
        <p class="page-subtitle">Daftar laporan Pendalaman Alkitab</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('laporan_pa.report') }}" class="btn-gkkd btn-outline-gkkd">
            <i class="fas fa-chart-bar"></i> Report Keaktifan
        </a>
        <a href="{{ route('laporan_pa.create') }}" class="btn-gkkd btn-primary-gkkd">
            <i class="fas fa-plus"></i> Input Laporan PA
        </a>
    </div>
</div>

@include('partials.data-filters', [
    'title' => 'Filter Laporan PA',
    'actionRoute' => 'laporan_pa.index',
    'exportRoute' => 'laporan_pa.export',
    'resetRoute' => 'laporan_pa.index',
    'filterIdPrefix' => 'laporan_pa',
    'wilayahParam' => 'wilayah_id',
    'pelayananParam' => 'pelayanan_id',
    'searchPlaceholder' => 'Anak, pembimbing, buku',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($laporanPas->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>Anak PA</th>
                        <th>Pembimbing</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th>Buku</th>
                        <th>Bab</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporanPas as $index => $laporan)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 500;">{{ $laporan->tanggal_pa->format('d/m/Y') }}</td>
                        <td style="font-weight: 600;">{{ $laporan->anakPa->nama_anak ?? '-' }}</td>
                        <td><span class="gkkd-badge badge-warning">{{ $laporan->pembimbing->nama_pembimbing ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-primary">{{ $laporan->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $laporan->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-info">{{ $laporan->nama_buku }}</span></td>
                        <td style="font-weight: 700;">{{ $laporan->bab }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan_pa.edit', $laporan->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('laporan_pa.destroy', $laporan->id) }}"
                                        data-name="{{ ($laporan->anakPa->nama_anak ?? '') . ' - ' . $laporan->tanggal_pa->format('d/m/Y') }}">
                                    <i class="fas fa-trash-alt"></i>
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
            <i class="fas fa-file-alt"></i>
            <p>Tidak ada laporan PA sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection
