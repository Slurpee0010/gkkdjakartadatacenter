@extends('layouts.app')

@section('title', 'Daftar Laporan Blesscomn')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span>Laporan Blesscomn
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Laporan Blesscomn</h1>
        <p class="page-subtitle">Data laporan kegiatan blesscomn</p>
    </div>
    <a href="{{ route('laporan_blesscomn.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Input Laporan
    </a>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($laporans->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>Blesscomn</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th class="text-center">Hadir (P)</th>
                        <th class="text-center">Hadir (W)</th>
                        <th class="text-center">Total Hadir</th>
                        <th class="text-center">Baru (P)</th>
                        <th class="text-center">Baru (W)</th>
                        <th class="text-center">Total Baru</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporans as $index => $item)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal_pelaksanaan->format('d M Y') }}</td>
                        <td style="font-weight: 600;">{{ $item->blesscomn->nama_blesscomn ?? '-' }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $item->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $item->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td class="text-center">{{ $item->hadir_pria }}</td>
                        <td class="text-center">{{ $item->hadir_wanita }}</td>
                        <td class="text-center">
                            <span style="font-weight: 700; color: var(--primary);">{{ $item->total_hadir }}</span>
                        </td>
                        <td class="text-center">{{ $item->baru_pria }}</td>
                        <td class="text-center">{{ $item->baru_wanita }}</td>
                        <td class="text-center">
                            <span style="font-weight: 700; color: var(--success);">{{ $item->total_baru }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan_blesscomn.edit', $item->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('laporan_blesscomn.destroy', $item->id) }}"
                                        data-name="laporan {{ $item->blesscomn->nama_blesscomn ?? '' }}">
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
            <i class="fas fa-clipboard-list"></i>
            <p>Belum ada data laporan blesscomn. Klik tombol "Input Laporan" untuk menambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
