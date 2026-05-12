@extends('layouts.app')

@section('title', 'Dashboard Kehadiran Ibadah')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Dashboard Kehadiran Ibadah
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : $filterWilayah;
@endphp
<div class="page-header mb-4">
    <h1 class="page-title"><i class="fas fa-chart-line me-2" style="color: var(--accent);"></i>Dashboard & Analytics Kehadiran Ibadah</h1>
    <p class="page-subtitle">Ringkasan mingguan dan rata-rata kehadiran berdasarkan Wilayah dan Pelayanan</p>
</div>

<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-filter me-2" style="color: var(--accent);"></i>Filter Global</h3>
    </div>
    <div class="gkkd-card-body">
        <form method="GET" action="{{ route('dashboard_kehadiran_ibadah') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="date_from">Tanggal Dari</label>
                    <input type="date" name="date_from" id="date_from" class="gkkd-form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="date_to">Tanggal Sampai</label>
                    <input type="date" name="date_to" id="date_to" class="gkkd-form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="id_wilayah">Wilayah</label>
                    @if($regionalLocked)
                        <input type="hidden" name="id_wilayah" value="{{ $regionalWilayahId }}">
                    @endif
                    <select name="id_wilayah" id="id_wilayah" class="gkkd-form-control" @disabled($regionalLocked)>
                        @unless($regionalLocked)
                            <option value="">Semua Wilayah</option>
                        @endunless
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}" {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>
                                {{ $wilayah->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="id_pelayanan">Pelayanan</label>
                    <select name="id_pelayanan" id="id_pelayanan" class="gkkd-form-control">
                        <option value="">Semua Pelayanan</option>
                        @foreach($pelayanans as $pelayanan)
                            <option value="{{ $pelayanan->id }}" {{ (string) $filterPelayanan === (string) $pelayanan->id ? 'selected' : '' }}>
                                {{ $pelayanan->nama_pelayanan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="periode">Periode Rata-rata</label>
                    <select name="periode" id="periode" class="gkkd-form-control">
                        <option value="1" {{ $periode === 1 ? 'selected' : '' }}>1 Bulan</option>
                        <option value="3" {{ $periode === 3 ? 'selected' : '' }}>3 Bulan</option>
                        <option value="6" {{ $periode === 6 ? 'selected' : '' }}>6 Bulan</option>
                        <option value="12" {{ $periode === 12 ? 'selected' : '' }}>1 Tahun</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn-gkkd btn-primary-gkkd w-100">
                        <i class="fas fa-search"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-1">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-value">{{ $stats['jumlah_ibadah'] }}</div>
            <div class="stat-label">Jumlah Ibadah</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-2">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value">{{ $stats['grand_total'] }}</div>
            <div class="stat-label">Total Kehadiran</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-3">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-video"></i></div>
            <div class="stat-value">{{ $stats['total_online'] }}</div>
            <div class="stat-label">Total Online</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-4">
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
            <div class="stat-value">{{ $stats['avg_grand_total'] }}</div>
            <div class="stat-label">Rata-rata Grand Total</div>
        </div>
    </div>
</div>

<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header flex-wrap gap-2">
        <h3 class="gkkd-card-title"><i class="fas fa-calendar-week me-2" style="color: var(--primary);"></i>Report A - Summary Per Minggu</h3>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dashboard_kehadiran_ibadah.export', array_merge(request()->query(), ['report' => 'A', 'format' => 'excel'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd" style="background: linear-gradient(135deg, #059669, #34d399);">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('dashboard_kehadiran_ibadah.export', array_merge(request()->query(), ['report' => 'A', 'format' => 'csv'])) }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                <i class="fas fa-file-csv"></i> CSV
            </a>
        </div>
    </div>
    <div class="gkkd-card-body">
        @if($reportA->count() > 0)
            <div class="analytics-bars mb-4">
                @foreach($reportA->take(8) as $row)
                    <div class="analytics-bar">
                        <div class="analytics-bar__meta">
                            <strong>{{ $row->minggu_label }}</strong>
                            <span>{{ $row->nama_pelayanan }} - {{ $row->nama_wilayah }}</span>
                        </div>
                        <div class="analytics-bar__track">
                            <div class="analytics-bar__fill analytics-bar__fill--primary" style="width: {{ round(($row->grand_total / $maxReportAGrand) * 100, 1) }}%;"></div>
                        </div>
                        <div class="analytics-bar__value">{{ $row->grand_total }}</div>
                    </div>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>Minggu</th>
                            <th>Wilayah</th>
                            <th>Pelayanan</th>
                            <th class="text-center">Jumlah Ibadah</th>
                            <th class="text-center">Total Onsite</th>
                            <th class="text-center">Total Online</th>
                            <th class="text-center">Total Baru</th>
                            <th class="text-center">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportA as $row)
                        <tr>
                            <td style="font-weight: 600;">{{ $row->minggu_label }}</td>
                            <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                            <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                            <td class="text-center">{{ $row->jumlah_ibadah }}</td>
                            <td class="text-center">{{ $row->total_onsite }}</td>
                            <td class="text-center">{{ $row->total_online }}</td>
                            <td class="text-center">{{ $row->total_baru }}</td>
                            <td class="text-center" style="font-weight: 800; color: var(--primary);">{{ $row->grand_total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data summary mingguan untuk filter ini.</p></div>
        @endif
    </div>
</div>

<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header flex-wrap gap-2">
        <h3 class="gkkd-card-title"><i class="fas fa-chart-bar me-2" style="color: var(--success);"></i>Report B - Rata-rata Kehadiran ({{ $periode }} Bulan)</h3>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dashboard_kehadiran_ibadah.export', array_merge(request()->query(), ['report' => 'B', 'format' => 'excel'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd" style="background: linear-gradient(135deg, #059669, #34d399);">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('dashboard_kehadiran_ibadah.export', array_merge(request()->query(), ['report' => 'B', 'format' => 'csv'])) }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                <i class="fas fa-file-csv"></i> CSV
            </a>
        </div>
    </div>
    <div class="gkkd-card-body">
        @if($reportB->count() > 0)
            <div class="analytics-bars mb-4">
                @foreach($reportB->take(8) as $row)
                    <div class="analytics-bar">
                        <div class="analytics-bar__meta">
                            <strong>{{ $row->nama_pelayanan }}</strong>
                            <span>{{ $row->nama_wilayah }} - {{ $row->jumlah_ibadah }} ibadah</span>
                        </div>
                        <div class="analytics-bar__track">
                            <div class="analytics-bar__fill analytics-bar__fill--success" style="width: {{ round(($row->avg_grand_total / $maxReportBAvg) * 100, 1) }}%;"></div>
                        </div>
                        <div class="analytics-bar__value">{{ $row->avg_grand_total }}</div>
                    </div>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>Wilayah</th>
                            <th>Pelayanan</th>
                            <th class="text-center">Jumlah Ibadah</th>
                            <th class="text-center">Avg Onsite</th>
                            <th class="text-center">Avg Online</th>
                            <th class="text-center">Avg Baru</th>
                            <th class="text-center">Avg Grand Total</th>
                            <th class="text-center">Total Grand</th>
                            <th class="text-center">Tanggal Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportB as $row)
                        <tr>
                            <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                            <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                            <td class="text-center">{{ $row->jumlah_ibadah }}</td>
                            <td class="text-center">{{ $row->avg_onsite }}</td>
                            <td class="text-center">{{ $row->avg_online }}</td>
                            <td class="text-center">{{ $row->avg_baru }}</td>
                            <td class="text-center" style="font-weight: 800; color: var(--success);">{{ $row->avg_grand_total }}</td>
                            <td class="text-center" style="font-weight: 700;">{{ $row->sum_grand_total }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_terakhir)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data rata-rata kehadiran untuk periode ini.</p></div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
    .analytics-bars {
        display: grid;
        gap: 12px;
    }
    .analytics-bar {
        display: grid;
        grid-template-columns: minmax(170px, 260px) 1fr 58px;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .analytics-bar:last-child {
        border-bottom: 0;
    }
    .analytics-bar__meta strong {
        display: block;
        font-size: 0.88rem;
        color: var(--text-primary);
        line-height: 1.25;
    }
    .analytics-bar__meta span {
        display: block;
        color: var(--text-secondary);
        font-size: 0.76rem;
        margin-top: 2px;
    }
    .analytics-bar__track {
        height: 10px;
        background: var(--border);
        border-radius: 999px;
        overflow: hidden;
    }
    .analytics-bar__fill {
        height: 100%;
        min-width: 4px;
        border-radius: 999px;
    }
    .analytics-bar__fill--primary {
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }
    .analytics-bar__fill--success {
        background: linear-gradient(90deg, var(--success), #34d399);
    }
    .analytics-bar__value {
        text-align: right;
        font-weight: 800;
        color: var(--text-primary);
    }
    @media (max-width: 767.98px) {
        .analytics-bar {
            grid-template-columns: 1fr 56px;
        }
        .analytics-bar__track {
            grid-column: 1 / -1;
            grid-row: 2;
        }
    }
</style>
@endsection
