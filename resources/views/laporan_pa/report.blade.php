@extends('layouts.app')

@section('title', 'Report Keaktifan PA')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('laporan_pa.index') }}">Laporan PA</a><span>/</span>Report
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Report Keaktifan PA</h1>
    <p class="page-subtitle">Rekap keaktifan Pendalaman Alkitab berdasarkan filter</p>
</div>

{{-- Filter Card --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-filter me-2" style="color: var(--accent);"></i>Filter Report</h3>
    </div>
    <div class="gkkd-card-body">
        <form method="GET" action="{{ route('laporan_pa.report') }}" id="reportFilterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="gkkd-form-group mb-0">
                        <label for="date_from" class="gkkd-form-label">Tanggal Dari</label>
                        <input type="date" name="date_from" id="date_from" class="gkkd-form-control"
                               value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="gkkd-form-group mb-0">
                        <label for="date_to" class="gkkd-form-label">Tanggal Sampai</label>
                        <input type="date" name="date_to" id="date_to" class="gkkd-form-control"
                               value="{{ request('date_to', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="gkkd-form-group mb-0">
                        <label for="wilayah_id" class="gkkd-form-label">Wilayah <small class="text-muted">(opsional)</small></label>
                        <select name="wilayah_id" id="wilayah_id" class="gkkd-form-control">
                            <option value="">— Semua Wilayah —</option>
                            @foreach ($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id }}" {{ request('wilayah_id') == $wilayah->id ? 'selected' : '' }}>
                                    {{ $wilayah->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="gkkd-form-group mb-0">
                        <label for="pelayanan_id" class="gkkd-form-label">Pelayanan <small class="text-muted">(opsional)</small></label>
                        <select name="pelayanan_id" id="pelayanan_id" class="gkkd-form-control">
                            <option value="">— Semua Pelayanan —</option>
                            @foreach ($pelayanans as $pelayanan)
                                <option value="{{ $pelayanan->id }}" {{ request('pelayanan_id') == $pelayanan->id ? 'selected' : '' }}>
                                    {{ $pelayanan->nama_pelayanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4 flex-wrap">
                <button type="submit" class="btn-gkkd btn-primary-gkkd">
                    <i class="fas fa-search"></i> Tampilkan Report
                </button>
                @if($reportData)
                <a href="{{ route('laporan_pa.export-csv', request()->query()) }}" class="btn-gkkd btn-accent-gkkd" style="background: linear-gradient(135deg, #059669, #34d399);">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
                @endif
                <a href="{{ route('laporan_pa.report') }}" class="btn-gkkd btn-outline-gkkd">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Report Results --}}
@if($reportData !== null)
<div class="gkkd-card fade-in">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title">
            <i class="fas fa-chart-bar me-2" style="color: #7c3aed;"></i>
            Hasil Report
            <span style="font-weight: 400; font-size: 0.82rem; color: var(--text-muted); margin-left: 8px;">
                {{ request('date_from') }} s/d {{ request('date_to') }}
            </span>
        </h3>
    </div>
    <div class="gkkd-card-body" style="padding: 0;">
        @if($reportData->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th>Nama Buku PA</th>
                        <th style="width: 200px;">Jumlah Anak PA Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $index => $row)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                        <td style="font-weight: 600;">{{ $row->nama_buku_display }}</td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 1.05rem; color: var(--primary);">
                                <i class="fas fa-users" style="font-size: 0.85rem;"></i>
                                {{ $row->jumlah_anak_aktif }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: rgba(37, 99, 235, 0.04);">
                        <td colspan="4" style="font-weight: 700; text-align: right; padding-right: 20px;">Total Keaktifan:</td>
                        <td>
                            <span style="font-weight: 800; font-size: 1.1rem; color: var(--primary);">
                                {{ $reportData->sum('jumlah_anak_aktif') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <p>Tidak ada data untuk filter yang dipilih.</p>
        </div>
        @endif
    </div>
</div>

{{-- Info Card --}}
<div class="gkkd-card fade-in mt-4" style="border-left: 4px solid var(--info);">
    <div class="gkkd-card-body" style="padding: 16px 20px;">
        <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">
            <i class="fas fa-info-circle" style="color: var(--info);"></i>
            <strong>Catatan Business Rule:</strong> Dalam 1 bulan, 1 Pembimbing yang melakukan PA dengan 1 Anak PA yang sama hanya dihitung <strong>1 Keaktifan</strong>,
            walaupun mereka PA lebih dari 2 kali di bulan tersebut. Angka di atas merupakan <em>Distinct Count</em> per bulan.
        </p>
    </div>
</div>
@endif
@endsection
