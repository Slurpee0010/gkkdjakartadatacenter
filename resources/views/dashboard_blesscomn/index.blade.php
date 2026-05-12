@extends('layouts.app')

@section('title', 'Dashboard Blesscomn')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Dashboard Blesscomn
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : $filterWilayah;
@endphp
<div class="page-header mb-4">
    <h1 class="page-title"><i class="fas fa-chart-line me-2" style="color: var(--accent);"></i>Dashboard & Analytics Blesscomn</h1>
    <p class="page-subtitle">Ringkasan data dan performa blesscomn gereja</p>
</div>

{{-- Global Filters --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-body" style="padding: 16px 24px;">
        <form method="GET" action="{{ route('dashboard_blesscomn') }}" class="d-flex flex-wrap align-items-end gap-3">
            <div style="min-width: 180px;">
                <label class="gkkd-form-label" style="margin-bottom: 4px;">Wilayah</label>
                @if($regionalLocked)
                    <input type="hidden" name="id_wilayah" value="{{ $regionalWilayahId }}">
                @endif
                <select name="id_wilayah" class="gkkd-form-control" @disabled($regionalLocked)>
                    @unless($regionalLocked)
                        <option value="">Semua Wilayah</option>
                    @endunless
                    @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}" {{ (string) $selectedWilayah === (string) $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 180px;">
                <label class="gkkd-form-label" style="margin-bottom: 4px;">Pelayanan</label>
                <select name="id_pelayanan" class="gkkd-form-control">
                    <option value="">Semua Pelayanan</option>
                    @foreach($pelayanans as $p)
                        <option value="{{ $p->id }}" {{ $filterPelayanan == $p->id ? 'selected' : '' }}>{{ $p->nama_pelayanan }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 160px;">
                <label class="gkkd-form-label" style="margin-bottom: 4px;">Periode</label>
                <select name="periode" class="gkkd-form-control">
                    <option value="1" {{ $periode == '1' ? 'selected' : '' }}>1 Bulan</option>
                    <option value="3" {{ $periode == '3' ? 'selected' : '' }}>3 Bulan</option>
                    <option value="6" {{ $periode == '6' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12" {{ $periode == '12' ? 'selected' : '' }}>1 Tahun</option>
                </select>
            </div>
            <button type="submit" class="btn-gkkd btn-primary-gkkd" style="height: 42px;">
                <i class="fas fa-filter"></i> Terapkan
            </button>
        </form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-1">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-church"></i></div>
            <div class="stat-value">{{ $reportA['total'] }}</div>
            <div class="stat-label">Total Blesscomn</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-2">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
            <div class="stat-value">{{ $reportB->count() }}</div>
            <div class="stat-label">Blesscomn Aktif ({{ $periode }} bln)</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-3">
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            <div class="stat-value">{{ $reportC->count() }}</div>
            <div class="stat-label">Leaderboard Entries</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 fade-in fade-in-delay-4">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-fire"></i></div>
            <div class="stat-value">{{ $reportD->count() }}</div>
            <div class="stat-label">Streaks Aktif</div>
        </div>
    </div>
</div>

{{-- Report A: Populasi --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-church me-2" style="color: var(--primary);"></i>Report A — Populasi Blesscomn</h3>
        <a href="{{ route('dashboard_blesscomn.export', array_merge(request()->query(), ['report' => 'A'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
    <div class="gkkd-card-body" style="padding: 0;">
        @if($reportA['per_wilayah']->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead><tr><th>Wilayah</th><th class="text-center">Jumlah Blesscomn</th></tr></thead>
                <tbody>
                    @foreach($reportA['per_wilayah'] as $row)
                    <tr>
                        <td><span class="gkkd-badge badge-primary">{{ $row->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td class="text-center" style="font-weight: 700;">{{ $row->jumlah }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: var(--surface);">
                        <td style="font-weight: 700;">Total</td>
                        <td class="text-center" style="font-weight: 800; color: var(--primary); font-size: 1.1rem;">{{ $reportA['total'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data populasi.</p></div>
        @endif
    </div>
</div>

{{-- Report B: Keaktifan --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-chart-bar me-2" style="color: var(--success);"></i>Report B — Rata-rata Keaktifan ({{ $periode }} Bulan)</h3>
        <a href="{{ route('dashboard_blesscomn.export', array_merge(request()->query(), ['report' => 'B'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
    <div class="gkkd-card-body" style="padding: 0;">
        @if($reportB->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead><tr><th>Blesscomn</th><th>Wilayah</th><th>Pelayanan</th><th class="text-center">Bulan Aktif</th><th class="text-center">Keaktifan</th></tr></thead>
                <tbody>
                    @foreach($reportB as $row)
                    <tr>
                        <td style="font-weight: 600;">{{ $row->nama_blesscomn }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                        <td class="text-center">{{ $row->bulan_aktif }} / {{ $periode }}</td>
                        <td class="text-center">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <div style="width: 80px; height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ $row->persentase }}%; height: 100%; background: {{ $row->persentase >= 75 ? 'var(--success)' : ($row->persentase >= 50 ? 'var(--warning)' : 'var(--danger)') }}; border-radius: 4px; transition: width 0.6s ease;"></div>
                                </div>
                                <span style="font-weight: 700; font-size: 0.82rem; color: {{ $row->persentase >= 75 ? 'var(--success)' : ($row->persentase >= 50 ? 'var(--warning)' : 'var(--danger)') }};">{{ $row->persentase }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data keaktifan untuk periode ini.</p></div>
        @endif
    </div>
</div>

{{-- Report C: Leaderboard --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-trophy me-2" style="color: var(--warning);"></i>Report C — Top Leaderboard ({{ $periode }} Bulan)</h3>
        <a href="{{ route('dashboard_blesscomn.export', array_merge(request()->query(), ['report' => 'C'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
    <div class="gkkd-card-body" style="padding: 0;">
        @if($reportC->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead><tr><th style="width:50px;">#</th><th>Blesscomn</th><th>Wilayah</th><th>Pelayanan</th><th class="text-center">Avg Hadir</th><th class="text-center">Total Baru</th><th class="text-center">Laporan</th></tr></thead>
                <tbody>
                    @foreach($reportC as $i => $row)
                    <tr>
                        <td>
                            @if($i < 3)
                                <span style="font-size: 1.2rem;">{{ ['🥇','🥈','🥉'][$i] }}</span>
                            @else
                                <span style="color: var(--text-muted); font-weight: 500;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 600;">{{ $row->nama_blesscomn }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                        <td class="text-center" style="font-weight: 700; color: var(--primary);">{{ $row->avg_hadir }}</td>
                        <td class="text-center" style="font-weight: 700; color: var(--success);">{{ $row->sum_baru }}</td>
                        <td class="text-center">{{ $row->jumlah_laporan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data leaderboard untuk periode ini.</p></div>
        @endif
    </div>
</div>

{{-- Report D: Streaks --}}
<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-fire me-2" style="color: var(--danger);"></i>Report D — Blesscomn Ter-Aktif (Streaks)</h3>
        <a href="{{ route('dashboard_blesscomn.export', array_merge(request()->query(), ['report' => 'D'])) }}" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
    <div class="gkkd-card-body" style="padding: 0;">
        @if($reportD->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead><tr><th>Blesscomn</th><th>Wilayah</th><th>Pelayanan</th><th class="text-center">Streak</th><th class="text-center">Bulan Terakhir</th></tr></thead>
                <tbody>
                    @foreach($reportD as $row)
                    <tr>
                        <td style="font-weight: 600;">{{ $row->nama_blesscomn }}</td>
                        <td><span class="gkkd-badge badge-primary">{{ $row->nama_wilayah }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $row->nama_pelayanan }}</span></td>
                        <td class="text-center">
                            <span style="background: linear-gradient(135deg, #ef4444, #f97316); color: #fff; padding: 4px 12px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;">
                                🔥 {{ $row->streak }} bulan
                            </span>
                        </td>
                        <td class="text-center">{{ $row->bulan_terakhir }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada blesscomn dengan streak aktif (min. 2 bulan berturut-turut).</p></div>
        @endif
    </div>
</div>
@endsection
