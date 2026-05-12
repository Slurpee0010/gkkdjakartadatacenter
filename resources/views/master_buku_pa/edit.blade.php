@extends('layouts.app')

@section('title', 'Edit Buku PA')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('master_buku_pa.index') }}">Master Buku PA</a><span>/</span>Edit
@endsection

@section('content')
<a href="{{ route('master_buku_pa.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Master Buku PA
</a>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-edit me-2" style="color: var(--info);"></i>Edit Buku PA</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('master_buku_pa.update', $masterBukuPa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="gkkd-form-group">
                        <label for="nama_buku" class="gkkd-form-label">Nama Buku</label>
                        <input type="text" name="nama_buku" id="nama_buku" class="gkkd-form-control" value="{{ old('nama_buku', $masterBukuPa->nama_buku) }}" required autofocus>
                    </div>
                    <div class="gkkd-form-group">
                        <label for="jumlah_bab" class="gkkd-form-label">Jumlah Bab</label>
                        <input type="number" name="jumlah_bab" id="jumlah_bab" class="gkkd-form-control" value="{{ old('jumlah_bab', $masterBukuPa->jumlah_bab) }}" min="1" required>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('master_buku_pa.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
