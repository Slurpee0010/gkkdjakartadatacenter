@extends('layouts.app')

@section('title', 'Manajemen User')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>User
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Manajemen User</h1>
        <p class="page-subtitle">Kelola akun, role, wilayah, dan status user.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-user-plus"></i> Tambah User
    </a>
</div>

<div class="gkkd-card fade-in mb-3">
    <div class="gkkd-card-body">
        <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-wrap gap-2">
            <input type="text" name="q" class="gkkd-form-control" style="max-width: 360px;" value="{{ $search }}" placeholder="Cari nama, email, atau UUID">
            <button type="submit" class="btn-gkkd btn-outline-gkkd">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search !== '')
                <a href="{{ route('users.index') }}" class="btn-gkkd btn-outline-gkkd">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($users->count() > 0)
            <div style="overflow-x: auto;">
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>UUID</th>
                            <th>Role</th>
                            <th>Wilayah</th>
                            <th>Status</th>
                            <th style="width: 280px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $statusClass = match ($user->status) {
                                    \App\Models\User::STATUS_ACTIVE => 'badge-success',
                                    \App\Models\User::STATUS_PENDING_DELETION => 'badge-warning',
                                    default => 'badge-info',
                                };
                                $currentUser = auth()->user();
                                $manageable = $user->role && in_array($user->role->name, $currentUser->assignableRoleNames(), true);
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">{{ $user->name }}</div>
                                    <div style="color: var(--text-secondary); font-size: 0.8rem;">{{ $user->email }}</div>
                                </td>
                                <td style="font-family: monospace; color: var(--text-secondary);">
                                    {{ $user->uuid ? \Illuminate\Support\Str::limit($user->uuid, 13, '') : '#' . $user->id }}
                                </td>
                                <td>
                                    <span class="gkkd-badge badge-primary">{{ $user->role?->label ?? '-' }}</span>
                                </td>
                                <td>{{ $user->wilayah?->nama_wilayah ?? '-' }}</td>
                                <td>
                                    <span class="gkkd-badge {{ $statusClass }}">{{ str_replace('_', ' ', $user->status) }}</span>
                                </td>
                                <td>
                                    @if($manageable)
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('users.edit', $user) }}" class="btn-gkkd btn-sm-gkkd btn-edit-gkkd">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            @if($currentUser->isSuperadmin() && ! $user->hasRole(\App\Models\Role::SUPERADMIN) && $user->isActive())
                                                <form method="POST" action="{{ route('users.impersonate', $user) }}" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                                                        <i class="fas fa-user-secret"></i> Impersonate
                                                    </button>
                                                </form>
                                            @endif

                                            @if($user->status !== \App\Models\User::STATUS_PENDING_DELETION)
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="m-0" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="reason" value="Diajukan dari halaman manajemen user">
                                                    <button type="submit" class="btn-gkkd btn-sm-gkkd btn-delete-gkkd">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.82rem;">Tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-users-cog"></i>
                <p>Belum ada user yang dapat ditampilkan.</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>
@endsection
