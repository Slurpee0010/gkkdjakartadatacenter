@extends('layouts.app')

@section('title', 'Inbox Broadcast')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Inbox
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="page-header mb-0">
        <h1 class="page-title">Inbox Broadcast</h1>
        <p class="page-subtitle">Pesan internal untuk superadmin dan admin.</p>
    </div>
    @if(auth()->user()?->hasPermissionTo('notifications', 'send'))
        <a href="{{ route('notifications.create') }}" class="btn-gkkd btn-primary-gkkd">
            <i class="fas fa-paper-plane"></i> Broadcast Pesan
        </a>
    @endif
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body">
        @forelse($notifications as $notification)
            <div style="border-bottom: 1px solid var(--border); padding: 18px 0;">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 4px;">{{ $notification->title }}</h3>
                        <div style="color: var(--text-secondary); font-size: 0.8rem;">
                            Dari {{ $notification->sender?->name ?? 'System' }}
                            @if($notification->sender?->role)
                                <span class="gkkd-badge badge-primary ms-1">{{ $notification->sender->role->label }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="color: var(--text-muted); font-size: 0.8rem;">{{ optional($notification->sent_at)->format('d M Y H:i') }}</div>
                </div>
                <p style="margin: 12px 0 0; color: var(--text-primary); line-height: 1.6;">{{ $notification->message }}</p>
                <div class="mt-2">
                    @foreach($notification->target_roles ?? [] as $role)
                        <span class="gkkd-badge badge-info">{{ config('rbac.roles.' . $role, $role) }}</span>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Belum ada pesan broadcast.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-3">
    {{ $notifications->links() }}
</div>
@endsection
