@extends('layouts.app')

@section('title', 'Ganti Password')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span>Ganti Password
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-key me-2" style="color: var(--accent);"></i>Ganti Password</h1>
    <p class="page-subtitle">Perbarui password akun Anda secara aman.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="gkkd-form-group">
                        <label for="current_password" class="gkkd-form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="gkkd-form-control" autocomplete="current-password" required>
                    </div>

                    <div class="gkkd-form-group">
                        <label for="new_password" class="gkkd-form-label">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="gkkd-form-control" autocomplete="new-password" required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Minimal 10 karakter, berisi huruf, angka, dan simbol.</small>
                    </div>

                    <div class="gkkd-form-group">
                        <label for="new_password_confirmation" class="gkkd-form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="gkkd-form-control" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn-gkkd btn-primary-gkkd">
                        <i class="fas fa-save"></i>
                        Simpan Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
