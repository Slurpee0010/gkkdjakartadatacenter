@extends('layouts.app')

@section('title', 'Tambah Pengurus Blesscomn')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('pengurus_blesscomn.index') }}">Pengurus Blesscomn</a><span>/</span>Tambah
@endsection

@section('content')
<a href="{{ route('pengurus_blesscomn.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pengurus
</a>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Tambah Pengurus Blesscomn</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('pengurus_blesscomn.store') }}" method="POST">
                    @csrf

                    {{-- Ketua --}}
                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                            <i class="fas fa-user-tie me-1"></i> Data Ketua
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label for="nama_ketua" class="gkkd-form-label">Nama Ketua</label>
                                    <input type="text" name="nama_ketua" id="nama_ketua" class="gkkd-form-control"
                                           placeholder="Masukkan nama ketua" value="{{ old('nama_ketua') }}" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label for="no_wa_ketua" class="gkkd-form-label">No. WhatsApp Ketua</label>
                                    <input type="text" name="no_wa_ketua" id="no_wa_ketua" class="gkkd-form-control"
                                           placeholder="Contoh: 08123456789" value="{{ old('no_wa_ketua') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Asisten --}}
                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--info); margin-bottom: 16px;">
                            <i class="fas fa-user-friends me-1"></i> Data Asisten
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label for="nama_asisten" class="gkkd-form-label">Nama Asisten</label>
                                    <input type="text" name="nama_asisten" id="nama_asisten" class="gkkd-form-control"
                                           placeholder="Masukkan nama asisten" value="{{ old('nama_asisten') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label for="no_wa_asisten" class="gkkd-form-label">No. WhatsApp Asisten</label>
                                    <input type="text" name="no_wa_asisten" id="no_wa_asisten" class="gkkd-form-control"
                                           placeholder="Contoh: 08123456789" value="{{ old('no_wa_asisten') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wilayah & Pelayanan --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="id_wilayah" class="gkkd-form-label">Wilayah</label>
                                <select name="id_wilayah" id="id_wilayah" class="gkkd-form-control" required>
                                    <option value="">— Pilih Wilayah —</option>
                                    @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}" {{ old('id_wilayah') == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama_wilayah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="id_pelayanan" class="gkkd-form-label">Pelayanan</label>
                                <select name="id_pelayanan" id="id_pelayanan" class="gkkd-form-control" required>
                                    <option value="">— Pilih Pelayanan —</option>
                                    @foreach ($pelayanans as $pelayanan)
                                        <option value="{{ $pelayanan->id }}" {{ old('id_pelayanan') == $pelayanan->id ? 'selected' : '' }}>
                                            {{ $pelayanan->nama_pelayanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('pengurus_blesscomn.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
