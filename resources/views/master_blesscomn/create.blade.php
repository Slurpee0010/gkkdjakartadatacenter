@extends('layouts.app')

@section('title', 'Tambah Master Blesscomn')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('master_blesscomn.index') }}">Master Blesscomn</a><span>/</span>Tambah
@endsection

@section('content')
<a href="{{ route('master_blesscomn.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Blesscomn
</a>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Tambah Master Blesscomn</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('master_blesscomn.store') }}" method="POST" id="formMasterBlesscomn">
                    @csrf

                    {{-- Nama & Tanggal --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="nama_blesscomn" class="gkkd-form-label">Nama Blesscomn</label>
                                <input type="text" name="nama_blesscomn" id="nama_blesscomn" class="gkkd-form-control"
                                       placeholder="Masukkan nama blesscomn" value="{{ old('nama_blesscomn') }}" required autofocus>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="tanggal_terbentuk" class="gkkd-form-label">Tanggal Terbentuk</label>
                                <input type="date" name="tanggal_terbentuk" id="tanggal_terbentuk" class="gkkd-form-control"
                                       value="{{ old('tanggal_terbentuk') }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Pengurus (Ketua + Asisten auto-fill) --}}
                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                            <i class="fas fa-user-shield me-1"></i> Pengurus
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label for="id_pengurus" class="gkkd-form-label">Nama Ketua</label>
                                    <select name="id_pengurus" id="id_pengurus" class="gkkd-form-control" required>
                                        <option value="">— Pilih Ketua —</option>
                                        @foreach ($pengurusList as $pengurus)
                                            <option value="{{ $pengurus->id }}"
                                                    data-asisten="{{ $pengurus->nama_asisten }}"
                                                    {{ old('id_pengurus') == $pengurus->id ? 'selected' : '' }}>
                                                {{ $pengurus->nama_ketua }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="gkkd-form-group">
                                    <label class="gkkd-form-label">Nama Asisten <span style="color: var(--text-muted); font-weight: 400;">(otomatis)</span></label>
                                    <input type="text" id="display_asisten" class="gkkd-form-control" readonly
                                           placeholder="Otomatis terisi saat memilih ketua"
                                           style="background: var(--surface-card); cursor: not-allowed; color: var(--text-secondary);">
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

                    <hr style="border-color: var(--border); margin: 24px 0;">

                    {{-- Pembelahan --}}
                    <div class="gkkd-form-group">
                        <label class="gkkd-form-label">Apakah hasil pembelahan?</label>
                        <div class="d-flex gap-4" style="margin-top: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.88rem; font-weight: 500;">
                                <input type="radio" name="is_pembelahan" value="0" {{ old('is_pembelahan', '0') == '0' ? 'checked' : '' }}
                                       style="accent-color: var(--primary); width: 18px; height: 18px;"> Tidak
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.88rem; font-weight: 500;">
                                <input type="radio" name="is_pembelahan" value="1" {{ old('is_pembelahan') == '1' ? 'checked' : '' }}
                                       style="accent-color: var(--warning); width: 18px; height: 18px;"> Ya, hasil pembelahan
                            </label>
                        </div>
                    </div>

                    {{-- Blesscomn Induk (tampil hanya jika pembelahan = ya) --}}
                    <div class="gkkd-form-group" id="indukContainer" style="display: none;">
                        <label for="id_blesscomn_induk" class="gkkd-form-label">
                            <i class="fas fa-code-branch me-1" style="color: var(--warning);"></i> Blesscomn Induk
                        </label>
                        <select name="id_blesscomn_induk" id="id_blesscomn_induk" class="gkkd-form-control">
                            <option value="">— Pilih Blesscomn Induk —</option>
                            @foreach ($blesscomnList as $bc)
                                <option value="{{ $bc->id }}" {{ old('id_blesscomn_induk') == $bc->id ? 'selected' : '' }}>
                                    {{ $bc->nama_blesscomn }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Pilih blesscomn induk asal pembelahan ini.
                        </small>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('master_blesscomn.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // ====================================
    // Auto-fill Asisten saat memilih Ketua
    // ====================================
    $('#id_pengurus').on('change', function() {
        var selected = $(this).find('option:selected');
        var asisten = selected.data('asisten') || '';
        $('#display_asisten').val(asisten);
    });

    // Trigger on load jika ada old value
    if ($('#id_pengurus').val()) {
        $('#id_pengurus').trigger('change');
    }

    // ====================================
    // Toggle Blesscomn Induk
    // ====================================
    $('input[name="is_pembelahan"]').on('change', function() {
        var isPembelahan = $(this).val() === '1';
        if (isPembelahan) {
            $('#indukContainer').slideDown(200);
            $('#id_blesscomn_induk').prop('required', true);
        } else {
            $('#indukContainer').slideUp(200);
            $('#id_blesscomn_induk').prop('required', false).val('');
        }
    });

    // Trigger on load
    var initialPembelahan = $('input[name="is_pembelahan"]:checked').val();
    if (initialPembelahan === '1') {
        $('#indukContainer').show();
        $('#id_blesscomn_induk').prop('required', true);
    }
});
</script>
@endsection
