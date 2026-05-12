@extends('layouts.app')

@section('title', 'Master Buku PA')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Master Buku PA
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Master Buku PA</h1>
        <p class="page-subtitle">Kelola data referensi buku Pendalaman Alkitab</p>
    </div>
    <a href="{{ route('master_buku_pa.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-plus"></i> Tambah Buku PA
    </a>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($bukuPas->count() > 0)
        <table class="gkkd-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Buku</th>
                    <th style="width: 120px;">Jumlah Bab</th>
                    <th>Status</th>
                    <th>Diajukan Oleh</th>
                    <th style="width: 260px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bukuPas as $index => $buku)
                @php
                    $statusClass = match ($buku->status) {
                        \App\Models\MasterBukuPa::STATUS_APPROVED => 'badge-success',
                        \App\Models\MasterBukuPa::STATUS_PENDING => 'badge-warning',
                        default => 'badge-info',
                    };
                    $canReview = auth()->user()?->isSuperadmin()
                        && $buku->status === \App\Models\MasterBukuPa::STATUS_PENDING;
                    $canManage = auth()->user()?->isSuperadmin() ?? false;
                @endphp
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $buku->nama_buku }}</td>
                    <td><span class="gkkd-badge badge-info">{{ $buku->jumlah_bab }} Bab</span></td>
                    <td><span class="gkkd-badge {{ $statusClass }}">{{ ucfirst($buku->status) }}</span></td>
                    <td>
                        <div>{{ $buku->requester?->name ?? '-' }}</div>
                        @if($buku->requested_at)
                            <small style="color: var(--text-muted);">{{ $buku->requested_at->format('d M Y H:i') }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            @if($canReview)
                                <form method="POST" action="{{ route('master_buku_pa.approve', $buku) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn-gkkd btn-sm-gkkd btn-accent-gkkd">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('master_buku_pa.reject', $buku) }}" class="m-0" onsubmit="return confirm('Tolak pengajuan buku ini?')">
                                    @csrf
                                    <input type="hidden" name="review_note" value="Ditolak dari halaman Master Buku PA">
                                    <button type="submit" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            @if($canManage)
                                <a href="{{ route('master_buku_pa.edit', $buku->id) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd btn-delete-swal"
                                        data-url="{{ route('master_buku_pa.destroy', $buku->id) }}"
                                        data-name="{{ $buku->nama_buku }}">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            @elseif(! $canReview)
                                <span style="color: var(--text-muted); font-size: 0.82rem;">Menunggu superadmin</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <p>Belum ada data buku PA. Klik tombol "Tambah Buku PA" untuk menambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
