@extends('layouts.app')

@section('title', 'Broadcast Pesan')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('notifications.index') }}">Inbox</a><span>/</span>Broadcast
@endsection

@section('content')
<a href="{{ route('notifications.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Inbox
</a>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-paper-plane me-2" style="color: var(--accent);"></i>Broadcast Pesan</h3>
            </div>
            <div class="gkkd-card-body">
                <form method="POST" action="{{ route('notifications.store') }}">
                    @csrf

                    <div class="gkkd-form-group">
                        <label for="title" class="gkkd-form-label">Judul</label>
                        <input type="text" name="title" id="title" class="gkkd-form-control" value="{{ old('title') }}" maxlength="120" required autofocus>
                    </div>

                    <div class="gkkd-form-group">
                        <label for="message" class="gkkd-form-label">Pesan</label>
                        <textarea name="message" id="message" class="gkkd-form-control" rows="6" maxlength="2000" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="gkkd-form-group">
                        <label class="gkkd-form-label">Target Role</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                            @foreach($allowedRoles as $roleName => $label)
                                <label style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer;">
                                    <input type="checkbox" name="target_roles[]" value="{{ $roleName }}" checked>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Role user tidak menerima broadcast inbox.</small>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-paper-plane"></i> Kirim Broadcast
                        </button>
                        <a href="{{ route('notifications.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
