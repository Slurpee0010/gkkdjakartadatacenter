@extends('layouts.app')

@section('title', 'Tambah User')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('users.index') }}">User</a><span>/</span>Tambah
@endsection

@section('content')
<a href="{{ route('users.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Manajemen User
</a>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-user-plus me-2" style="color: var(--accent);"></i>Tambah User</h3>
            </div>
            <div class="gkkd-card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="gkkd-form-group">
                        <label for="name" class="gkkd-form-label">Nama</label>
                        <input type="text" name="name" id="name" class="gkkd-form-control" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="gkkd-form-group">
                        <label for="email" class="gkkd-form-label">Email</label>
                        <input type="email" name="email" id="email" class="gkkd-form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="id_role" class="gkkd-form-label">Role</label>
                                <select name="id_role" id="id_role" class="gkkd-form-control" required>
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @selected(old('id_role') === $role->name)>{{ $role->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="id_wilayah" class="gkkd-form-label">Wilayah</label>
                                <select name="id_wilayah" id="id_wilayah" class="gkkd-form-control" required>
                                    <option value="">Pilih Wilayah</option>
                                    @foreach($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}" @selected((string) old('id_wilayah') === (string) $wilayah->id)>{{ $wilayah->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="password" class="gkkd-form-label">Password</label>
                                <input type="password" name="password" id="password" class="gkkd-form-control" autocomplete="new-password" required>
                                <small style="color: var(--text-muted); font-size: 0.75rem;">Minimal 10 karakter.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="password_confirmation" class="gkkd-form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="gkkd-form-control" autocomplete="new-password" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('users.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
