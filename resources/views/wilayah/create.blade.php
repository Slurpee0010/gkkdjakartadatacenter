@extends('layouts.app')

@section('title', 'Tambah Wilayah')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('wilayah.index') }}">Wilayah</a><span>/</span>Tambah
@endsection

@section('content')
<a href="{{ route('wilayah.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Wilayah
</a>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Tambah Wilayah</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('wilayah.store') }}" method="POST">
                    @csrf
                    <div class="gkkd-form-group">
                        <label for="nama_wilayah" class="gkkd-form-label">Nama Wilayah</label>
                        <input type="text" name="nama_wilayah" id="nama_wilayah" class="gkkd-form-control" placeholder="Masukkan nama wilayah" value="{{ old('nama_wilayah') }}" required autofocus>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('wilayah.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
