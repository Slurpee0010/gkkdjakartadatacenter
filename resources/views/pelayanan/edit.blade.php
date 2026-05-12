@extends('layouts.app')

@section('title', 'Edit Pelayanan')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('pelayanan.index') }}">Pelayanan</a><span>/</span>Edit
@endsection

@section('content')
<a href="{{ route('pelayanan.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pelayanan
</a>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-edit me-2" style="color: var(--info);"></i>Edit Pelayanan</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('pelayanan.update', $pelayanan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="gkkd-form-group">
                        <label for="nama_pelayanan" class="gkkd-form-label">Nama Pelayanan</label>
                        <input type="text" name="nama_pelayanan" id="nama_pelayanan" class="gkkd-form-control" value="{{ old('nama_pelayanan', $pelayanan->nama_pelayanan) }}" required autofocus>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('pelayanan.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
