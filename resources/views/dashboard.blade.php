@extends('layouts.app')

@section('title', 'Dashboard Laporan PA')
@section('breadcrumb', 'Dashboard Laporan PA')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Laporan PA</h1>
    <p class="page-subtitle">Selamat datang di Sistem Manajemen GKKD Jakarta</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-xl">
        <div class="stat-card stat-primary fade-in fade-in-delay-1">
            <div class="stat-icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="stat-value">{{ $totalWilayah }}</div>
            <div class="stat-label">Total Wilayah</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card stat-success fade-in fade-in-delay-2">
            <div class="stat-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="stat-value">{{ $totalPelayanan }}</div>
            <div class="stat-label">Total Pelayanan</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card stat-warning fade-in fade-in-delay-3">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-value">{{ $totalPembimbing }}</div>
            <div class="stat-label">Total Pembimbing</div>
        </div>
    </div>
    <div class="col-6 col-xl">
        <div class="stat-card stat-info fade-in fade-in-delay-4">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ $totalAnakBimbingan }}</div>
            <div class="stat-label">Total Anak PA</div>
        </div>
    </div>
    <div class="col-12 col-xl">
        <div class="stat-card stat-primary fade-in fade-in-delay-4" style="--primary: #7c3aed;">
            <div class="stat-icon" style="background: rgba(124,58,237,0.08); color: #7c3aed;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-value">{{ $totalLaporanPa }}</div>
            <div class="stat-label">Total Laporan PA</div>
        </div>
    </div>
</div>

<!-- Recent Data Tables -->
<div class="row g-4">
    <!-- Recent Pembimbing -->
    <div class="col-lg-6">
        <div class="gkkd-card fade-in fade-in-delay-3">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-user-tie me-2" style="color: var(--accent);"></i>Pembimbing Terbaru</h3>
                <a href="{{ route('pembimbing.index') }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                    Lihat Semua <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                </a>
            </div>
            <div class="gkkd-card-body" style="padding: 0;">
                @if($recentPembimbing->count() > 0)
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Wilayah</th>
                            <th>Pelayanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentPembimbing as $pembimbing)
                        <tr>
                            <td style="font-weight: 600;">{{ $pembimbing->nama_pembimbing }}</td>
                            <td><span class="gkkd-badge badge-primary">{{ $pembimbing->wilayah->nama_wilayah ?? '-' }}</span></td>
                            <td><span class="gkkd-badge badge-success">{{ $pembimbing->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-user-tie"></i>
                    <p>Belum ada data pembimbing</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Anak Bimbingan -->
    <div class="col-lg-6">
        <div class="gkkd-card fade-in fade-in-delay-4">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-users me-2" style="color: var(--info);"></i>Anak PA Terbaru</h3>
                <a href="{{ route('anak_bimbingan.index') }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                    Lihat Semua <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                </a>
            </div>
            <div class="gkkd-card-body" style="padding: 0;">
                @if($recentAnakBimbingan->count() > 0)
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>Pembimbing</th>
                            <th>Wilayah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentAnakBimbingan as $anak)
                        <tr>
                            <td style="font-weight: 600;">{{ $anak->nama_anak }}</td>
                            <td><span class="gkkd-badge badge-warning">{{ $anak->pembimbing->nama_pembimbing ?? '-' }}</span></td>
                            <td><span class="gkkd-badge badge-primary">{{ $anak->wilayah->nama_wilayah ?? '-' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Belum ada data anak bimbingan</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Laporan PA -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-file-alt me-2" style="color: #7c3aed;"></i>Laporan PA Terbaru</h3>
                <a href="{{ route('laporan_pa.index') }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                    Lihat Semua <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                </a>
            </div>
            <div class="gkkd-card-body" style="padding: 0;">
                @if($recentLaporanPa->count() > 0)
                <div class="table-responsive">
                    <table class="gkkd-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Anak PA</th>
                                <th>Pembimbing</th>
                                <th>Buku</th>
                                <th>Bab</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentLaporanPa as $laporan)
                            <tr>
                                <td>{{ $laporan->tanggal_pa->format('d/m/Y') }}</td>
                                <td style="font-weight: 600;">{{ $laporan->anakPa->nama_anak ?? '-' }}</td>
                                <td><span class="gkkd-badge badge-warning">{{ $laporan->pembimbing->nama_pembimbing ?? '-' }}</span></td>
                                <td><span class="gkkd-badge badge-info">{{ $laporan->nama_buku }}</span></td>
                                <td style="font-weight: 600;">{{ $laporan->bab }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <p>Belum ada laporan PA</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
