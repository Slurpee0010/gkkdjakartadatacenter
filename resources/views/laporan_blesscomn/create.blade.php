@extends('layouts.app')

@section('title', 'Input Laporan Blesscomn')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('laporan_blesscomn.index') }}">Laporan Blesscomn</a><span>/</span>Input
@endsection

@section('content')
<a href="{{ route('laporan_blesscomn.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Laporan
</a>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title">
                    <i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Input Laporan Blesscomn
                </h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('laporan_blesscomn.store') }}" method="POST" id="formLaporanBlesscomn">
                    @csrf

                    <div class="gkkd-form-group">
                        <label for="tanggal_pelaksanaan" class="gkkd-form-label">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan"
                               class="gkkd-form-control"
                               value="{{ old('tanggal_pelaksanaan', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>

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

                    <div class="gkkd-form-group">
                        <label for="id_blesscomn" class="gkkd-form-label">Blesscomn</label>
                        <select name="id_blesscomn" id="id_blesscomn" class="gkkd-form-control" required disabled>
                            <option value="">— Pilih Wilayah & Pelayanan terlebih dahulu —</option>
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Daftar blesscomn difilter berdasarkan Wilayah & Pelayanan yang dipilih.
                        </small>
                    </div>

                    <hr style="border-color: var(--border); margin: 24px 0;">

                    {{-- Kehadiran --}}
                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                            <i class="fas fa-users me-1"></i> Data Kehadiran
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="hadir_pria" class="gkkd-form-label">Hadir Pria</label>
                                    <input type="number" name="hadir_pria" id="hadir_pria"
                                           class="gkkd-form-control calc-hadir"
                                           value="{{ old('hadir_pria', 0) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="hadir_wanita" class="gkkd-form-label">Hadir Wanita</label>
                                    <input type="number" name="hadir_wanita" id="hadir_wanita"
                                           class="gkkd-form-control calc-hadir"
                                           value="{{ old('hadir_wanita', 0) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label class="gkkd-form-label">Total Hadir</label>
                                    <div id="totalHadirDisplay" class="total-display total-display--primary">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Jiwa Baru --}}
                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--success); margin-bottom: 16px;">
                            <i class="fas fa-user-plus me-1"></i> Jiwa Baru
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="baru_pria" class="gkkd-form-label">Baru Pria</label>
                                    <input type="number" name="baru_pria" id="baru_pria"
                                           class="gkkd-form-control calc-baru"
                                           value="{{ old('baru_pria', 0) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="baru_wanita" class="gkkd-form-label">Baru Wanita</label>
                                    <input type="number" name="baru_wanita" id="baru_wanita"
                                           class="gkkd-form-control calc-baru"
                                           value="{{ old('baru_wanita', 0) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label class="gkkd-form-label">Total Baru</label>
                                    <div id="totalBaruDisplay" class="total-display total-display--success">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan Laporan
                        </button>
                        <a href="{{ route('laporan_blesscomn.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .total-display {
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 1.3rem;
        font-weight: 800;
        text-align: center;
        letter-spacing: -0.02em;
        color: #fff;
        transition: transform 0.15s ease;
    }
    .total-display--primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
    }
    .total-display--success {
        background: linear-gradient(135deg, var(--success), #34d399);
    }
</style>
<script>
$(document).ready(function() {
    // Cascading: Wilayah/Pelayanan → Blesscomn
    function loadBlesscomn() {
        var wId = $('#id_wilayah').val();
        var pId = $('#id_pelayanan').val();
        if (wId && pId) {
            $.ajax({
                url: '{{ route("api.get-blesscomn") }}',
                data: { id_wilayah: wId, id_pelayanan: pId },
                dataType: 'json',
                success: function(data) {
                    var sel = $('#id_blesscomn');
                    sel.html('<option value="">— Pilih Blesscomn —</option>');
                    $.each(data, function(i, item) {
                        sel.append('<option value="' + item.id + '">' + item.nama_blesscomn + '</option>');
                    });
                    sel.prop('disabled', false);
                    if (data.length === 0) {
                        sel.html('<option value="">— Tidak ada blesscomn untuk filter ini —</option>');
                    }
                }
            });
        } else {
            $('#id_blesscomn')
                .html('<option value="">— Pilih Wilayah & Pelayanan terlebih dahulu —</option>')
                .prop('disabled', true);
        }
    }
    $('#id_wilayah, #id_pelayanan').on('change', loadBlesscomn);

    // Real-time total calculation
    function calcTotal(cls, disp) {
        var total = 0;
        $(cls).each(function() { total += parseInt($(this).val()) || 0; });
        $(disp).text(total);
        $(disp).css('transform', 'scale(1.08)');
        setTimeout(function() { $(disp).css('transform', 'scale(1)'); }, 150);
    }
    $('.calc-hadir').on('input change', function() { calcTotal('.calc-hadir', '#totalHadirDisplay'); });
    $('.calc-baru').on('input change', function() { calcTotal('.calc-baru', '#totalBaruDisplay'); });
    calcTotal('.calc-hadir', '#totalHadirDisplay');
    calcTotal('.calc-baru', '#totalBaruDisplay');

    // Trigger on load if old values exist
    if ($('#id_wilayah').val() && $('#id_pelayanan').val()) { loadBlesscomn(); }
});
</script>
@endsection
