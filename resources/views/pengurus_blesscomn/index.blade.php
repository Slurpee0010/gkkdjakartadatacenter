@extends('layouts.app')

@section('title', 'Daftar Pengurus Blesscomn')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Pengurus Blesscomn
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Daftar Pengurus Blesscomn</h1>
        <p class="page-subtitle">Kelola data pengurus (ketua & asisten) blesscomn</p>
    </div>
    <a href="{{ route('pengurus_blesscomn.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Pengurus
    </a>
</div>

@include('partials.data-filters', [
    'title' => 'Filter Pengurus Blesscomn',
    'actionRoute' => 'pengurus_blesscomn.index',
    'exportRoute' => 'pengurus_blesscomn.export',
    'resetRoute' => 'pengurus_blesscomn.index',
    'filterIdPrefix' => 'pengurus_blesscomn',
    'wilayahParam' => 'id_wilayah',
    'pelayananParam' => 'id_pelayanan',
    'searchPlaceholder' => 'Ketua, asisten, no. WA',
])

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($pengurus->count() > 0)
        <div class="table-responsive">
            <table class="gkkd-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Ketua</th>
                        <th>No. WA Ketua</th>
                        <th>Nama Asisten</th>
                        <th>No. WA Asisten</th>
                        <th>Wilayah</th>
                        <th>Pelayanan</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengurus as $index => $item)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $item->nama_ketua }}</td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $item->no_wa_ketua) }}" target="_blank" style="color: var(--success); text-decoration: none;">
                                <i class="fab fa-whatsapp"></i> {{ $item->no_wa_ketua }}
                            </a>
                        </td>
                        <td>{{ $item->nama_asisten }}</td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $item->no_wa_asisten) }}" target="_blank" style="color: var(--success); text-decoration: none;">
                                <i class="fab fa-whatsapp"></i> {{ $item->no_wa_asisten }}
                            </a>
                        </td>
                        <td><span class="gkkd-badge badge-primary">{{ $item->wilayah->nama_wilayah ?? '-' }}</span></td>
                        <td><span class="gkkd-badge badge-success">{{ $item->pelayanan->nama_pelayanan ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pengurus_blesscomn.edit', $item->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('pengurus_blesscomn.destroy', $item->id) }}"
                                        data-name="{{ $item->nama_ketua }}">
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
            <i class="fas fa-user-shield"></i>
            <p>Tidak ada pengurus blesscomn sesuai filter atau search.</p>
        </div>
        @endif
    </div>
</div>
@endsection
