@extends('layouts.app')

@section('title', 'Tambah Pembimbing')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('pembimbing.index') }}">Pembimbing</a><span>/</span>Tambah
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : old('wilayah_id');
@endphp
<a href="{{ route('pembimbing.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pembimbing
</a>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Tambah Pembimbing</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('pembimbing.store') }}" method="POST">
                    @csrf
                    <div class="gkkd-form-group">
                        <label for="nama_pembimbing" class="gkkd-form-label">Nama Pembimbing</label>
                        <input type="text" name="nama_pembimbing" id="nama_pembimbing" class="gkkd-form-control" placeholder="Masukkan nama pembimbing" value="{{ old('nama_pembimbing') }}" required autofocus>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="wilayah_id" class="gkkd-form-label">Wilayah</label>
                                @if($regionalLocked)
                                    <input type="hidden" name="wilayah_id" value="{{ $regionalWilayahId }}">
                                @endif
                                <select name="wilayah_id" id="wilayah_id" class="gkkd-form-control" required @disabled($regionalLocked)>
                                    @unless($regionalLocked)
                                        <option value="">— Pilih Wilayah —</option>
                                    @endunless
                                    @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}" {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>{{ $wilayah->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="pelayanan_id" class="gkkd-form-label">Pelayanan</label>
                                <select name="pelayanan_id" id="pelayanan_id" class="gkkd-form-control" required>
                                    <option value="">— Pilih Pelayanan —</option>
                                    @foreach ($pelayanans as $pelayanan)
                                        <option value="{{ $pelayanan->id }}" {{ old('pelayanan_id') == $pelayanan->id ? 'selected' : '' }}>{{ $pelayanan->nama_pelayanan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('pembimbing.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
