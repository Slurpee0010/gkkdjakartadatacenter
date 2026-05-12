@extends('layouts.app')

@section('title', 'System Event Log')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>System Event Log
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-shield-alt me-2" style="color: var(--accent);"></i>System Event Log</h1>
    <p class="page-subtitle">Audit trail aktivitas login, input, edit, delete, dan ganti password.</p>
</div>

<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-body">
        <form method="GET" action="{{ route('audit_logs.index') }}">
            <div class="row g-3">
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="date_from">Dari</label>
                    <input type="date" name="date_from" id="date_from" class="gkkd-form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="date_to">Sampai</label>
                    <input type="date" name="date_to" id="date_to" class="gkkd-form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="event">Event</label>
                    <select name="event" id="event" class="gkkd-form-control">
                        <option value="">Semua</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="module">Modul</label>
                    <select name="module" id="module" class="gkkd-form-control">
                        <option value="">Semua</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="actor_role">Role</label>
                    <select name="actor_role" id="actor_role" class="gkkd-form-control">
                        <option value="">Semua</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @selected(request('actor_role') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="gkkd-form-label" for="search">Search</label>
                    <input type="search" name="search" id="search" class="gkkd-form-control" value="{{ request('search') }}" placeholder="User, email, objek, IP">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 flex-wrap">
                <button type="submit" class="btn-gkkd btn-primary-gkkd">
                    <i class="fas fa-search"></i>
                    Tampilkan
                </button>
                <a href="{{ route('audit_logs.index') }}" class="btn-gkkd btn-outline-gkkd">
                    <i class="fas fa-undo"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="gkkd-card fade-in">
    <div class="gkkd-card-body" style="padding: 0;">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="gkkd-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Event</th>
                            <th>Aktor</th>
                            <th>Modul</th>
                            <th>Objek</th>
                            <th>IP</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="white-space: nowrap;">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                                <td><span class="gkkd-badge badge-info">{{ $log->event }}</span></td>
                                <td>
                                    <strong>{{ $log->actor_name ?? 'Guest/System' }}</strong><br>
                                    <small style="color: var(--text-muted);">{{ $log->actor_email ?? '-' }} {{ $log->actor_role ? '(' . $log->actor_role . ')' : '' }}</small>
                                </td>
                                <td>{{ $log->module ?? '-' }}</td>
                                <td>
                                    <strong>{{ $log->auditable_label ?? '-' }}</strong><br>
                                    <small style="color: var(--text-muted);">{{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id ?? '-' }}</small>
                                </td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td style="min-width: 260px;">
                                    @if($log->event === \App\Models\AuditLog::EVENT_PASSWORD_CHANGED)
                                        <span class="gkkd-badge badge-warning">Password changed</span>
                                        <div style="color: var(--text-muted); font-size: 0.76rem; margin-top: 4px;">
                                            Nilai password tidak disimpan di log.
                                        </div>
                                    @else
                                        @if($log->old_values)
                                            <details>
                                                <summary>Old values</summary>
                                                <pre style="white-space: pre-wrap; font-size: 0.75rem;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </details>
                                        @endif
                                        @if($log->new_values)
                                            <details>
                                                <summary>New values</summary>
                                                <pre style="white-space: pre-wrap; font-size: 0.75rem;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </details>
                                        @endif
                                    @endif
                                    @if($log->metadata)
                                        <details>
                                            <summary>Metadata</summary>
                                            <pre style="white-space: pre-wrap; font-size: 0.75rem;">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </details>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 24px;">
                {{ $logs->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Belum ada event log untuk filter ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
