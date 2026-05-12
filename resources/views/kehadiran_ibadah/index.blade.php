@extends('layouts.app')

@section('title', 'Kehadiran Ibadah')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Kehadiran Ibadah
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Manajemen Kehadiran Ibadah</h1>
        <p class="page-subtitle">Input, kelola, dan export data kehadiran ibadah onsite, online, dan jiwa baru</p>
    </div>
    <a href="{{ route('kehadiran_ibadah.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Input Kehadiran
    </a>
</div>

@include('partials.data-filters', [
    'title' => 'Filter Kehadiran Ibadah',
    'actionRoute' => 'kehadiran_ibadah.index',
    'exportRoute' => 'kehadiran_ibadah.export',
    'resetRoute' => 'kehadiran_ibadah.index',
    'filterIdPrefix' => 'kehadiran_ibadah',
    'wilayahParam' => 'id_wilayah',
    'pelayananParam' => 'id_pelayanan',
    'searchPlaceholder' => 'Nama ibadah, wilayah, pelayanan',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($ibadahs->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table kehadiran-table">
                <thead>
                    <tr>
                        <th style="width: 48px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama Ibadah</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th class="text-center">Onsite P</th>
                        <th class="text-center">Onsite W</th>
                        <th class="text-center">Total Onsite</th>
                        <th class="text-center">Online P</th>
                        <th class="text-center">Online W</th>
                        <th class="text-center">Total Online</th>
                        <th class="text-center">Baru P</th>
                        <th class="text-center">Baru W</th>
                        <th class="text-center">Total Baru</th>
                        <th class="text-center">Grand Total</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ibadahs as $index => $item)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal_ibadah->format('d M Y') }}</td>
                        <td>
                            <div style="font-weight: 700;">{{ $item->nama_ibadah }}</div>
                            @if($item->is_nama_manual)
                                <span class="gkkd-badge badge-warning mt-1">Manual</span>
                            @endif
                        </td>
                        <td><span class="gkkd-badge badge-primary">{{ $item->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $item->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td class="text-center">{{ $item->hadir_pria_onsite }}</td>
                        <td class="text-center">{{ $item->hadir_wanita_onsite }}</td>
                        <td class="text-center"><span class="total-pill total-pill--primary">{{ $item->total_hadir_onsite }}</span></td>
                        <td class="text-center">{{ $item->hadir_pria_online }}</td>
                        <td class="text-center">{{ $item->hadir_wanita_online }}</td>
                        <td class="text-center"><span class="total-pill total-pill--info">{{ $item->total_hadir_online }}</span></td>
                        <td class="text-center">{{ $item->baru_pria }}</td>
                        <td class="text-center">{{ $item->baru_wanita }}</td>
                        <td class="text-center"><span class="total-pill total-pill--success">{{ $item->total_baru }}</span></td>
                        <td class="text-center"><span class="total-pill total-pill--grand">{{ $item->grand_total }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kehadiran_ibadah.edit', $item->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('kehadiran_ibadah.destroy', $item->id) }}"
                                        data-name="{{ $item->nama_ibadah }}">
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
            <p>Tidak ada data kehadiran ibadah sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
    .kehadiran-table td:nth-child(3) {
        min-width: 220px;
    }
    .total-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 4px 9px;
        border-radius: 7px;
        font-weight: 800;
        font-size: 0.78rem;
    }
    .total-pill--primary { background: rgba(30, 58, 95, 0.1); color: var(--primary); }
    .total-pill--info { background: rgba(59, 130, 246, 0.1); color: var(--info); }
    .total-pill--success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .total-pill--grand {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: #fff;
        min-width: 54px;
    }
</style>
@endsection
