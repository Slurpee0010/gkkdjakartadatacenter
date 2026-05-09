@extends('layouts.app')

@section('title', 'Input Laporan PA')
@section('breadcrumb')
<a href="{{ url('/dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('laporan_pa.index') }}">Laporan PA</a><span>/</span>Input
@endsection

@section('content')
<a href="{{ route('laporan_pa.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Laporan PA
</a>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Input Laporan PA</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('laporan_pa.store') }}" method="POST" id="formLaporanPa">
                    @csrf

                    {{-- Tanggal PA --}}
                    <div class="gkkd-form-group">
                        <label for="tanggal_pa" class="gkkd-form-label">Tanggal PA</label>
                        <input type="date" name="tanggal_pa" id="tanggal_pa" class="gkkd-form-control"
                               value="{{ old('tanggal_pa', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Wilayah & Pelayanan --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="wilayah_id" class="gkkd-form-label">Wilayah</label>
                                <select name="wilayah_id" id="wilayah_id" class="gkkd-form-control" required>
                                    <option value="">— Pilih Wilayah —</option>
                                    @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}" {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama_wilayah }}
                                        </option>
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
                                        <option value="{{ $pelayanan->id }}" {{ old('pelayanan_id') == $pelayanan->id ? 'selected' : '' }}>
                                            {{ $pelayanan->nama_pelayanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Pembimbing (cascading) --}}
                    <div class="gkkd-form-group">
                        <label for="pembimbing_id" class="gkkd-form-label">Pembimbing</label>
                        <select name="pembimbing_id" id="pembimbing_id" class="gkkd-form-control" required disabled>
                            <option value="">— Pilih Wilayah & Pelayanan terlebih dahulu —</option>
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Daftar pembimbing difilter berdasarkan Wilayah & Pelayanan yang dipilih.
                        </small>
                    </div>

                    {{-- Ticket 2: Multi-Select Anak PA --}}
                    <div class="gkkd-form-group">
                        <label class="gkkd-form-label">Anak PA <span style="font-weight: 400; color: var(--text-muted);">(Pilih satu atau lebih)</span></label>
                        <div id="anakPaContainer" class="anak-pa-checklist" style="border: 1px solid var(--border); border-radius: 10px; padding: 12px 16px; min-height: 48px; background: var(--surface);">
                            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                                <i class="fas fa-info-circle"></i> Pilih Pembimbing terlebih dahulu untuk memuat daftar anak PA.
                            </p>
                        </div>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-users"></i> Centang anak PA yang mengikuti PA kelompok ini.
                        </small>
                    </div>

                    <hr style="border-color: var(--border); margin: 24px 0;">

                    {{-- Buku PA --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="buku_pa_id" class="gkkd-form-label">Buku PA</label>
                                {{-- Ticket 1: Tidak ada required, supaya "Lainnya" bisa null --}}
                                <select name="buku_pa_id" id="buku_pa_id" class="gkkd-form-control">
                                    <option value="">— Pilih Buku PA —</option>
                                    @foreach ($bukuPas as $buku)
                                        <option value="{{ $buku->id }}"
                                                data-jumlah-bab="{{ $buku->jumlah_bab }}"
                                                {{ old('buku_pa_id') == $buku->id ? 'selected' : '' }}>
                                            {{ $buku->nama_buku }} ({{ $buku->jumlah_bab }} bab)
                                        </option>
                                    @endforeach
                                    <option value="lainnya" {{ old('buku_pa_id') == 'lainnya' ? 'selected' : '' }}>
                                        ✏️ Lainnya (Tulis Sendiri)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="bab" class="gkkd-form-label">Bab</label>
                                <div id="babContainer">
                                    <input type="number" name="bab" id="babInput" class="gkkd-form-control"
                                           placeholder="Pilih buku terlebih dahulu" value="{{ old('bab') }}"
                                           min="1" required disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Buku Lainnya (hidden by default) --}}
                    <div class="gkkd-form-group" id="bukuLainnyaContainer" style="display: none;">
                        <label for="buku_pa_lainnya" class="gkkd-form-label">Nama Buku PA (Lainnya)</label>
                        <input type="text" name="buku_pa_lainnya" id="buku_pa_lainnya" class="gkkd-form-control"
                               placeholder="Tulis nama buku PA..." value="{{ old('buku_pa_lainnya') }}">
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Simpan Laporan
                        </button>
                        <a href="{{ route('laporan_pa.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .anak-pa-checklist { max-height: 240px; overflow-y: auto; }
    .anak-pa-checklist label {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 4px; margin: 0; cursor: pointer; border-radius: 6px;
        font-size: 0.88rem; font-weight: 500; color: var(--text-primary);
        transition: background 0.15s;
    }
    .anak-pa-checklist label:hover { background: rgba(37, 99, 235, 0.04); }
    .anak-pa-checklist input[type="checkbox"] {
        width: 18px; height: 18px; accent-color: var(--primary);
        cursor: pointer; flex-shrink: 0;
    }
    .anak-pa-checklist .select-all-bar {
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid var(--border); padding-bottom: 8px; margin-bottom: 4px;
    }
    .anak-pa-checklist .select-all-bar small { color: var(--text-muted); font-size: 0.78rem; }
</style>
<script>
$(document).ready(function() {

    // =========================================
    // 1. CASCADING: Wilayah/Pelayanan → Pembimbing
    // =========================================
    function loadPembimbing() {
        var wilayahId = $('#wilayah_id').val();
        var pelayananId = $('#pelayanan_id').val();

        if (wilayahId && pelayananId) {
            $.ajax({
                url: '{{ route("api.get-pembimbing") }}',
                data: { wilayah_id: wilayahId, pelayanan_id: pelayananId },
                dataType: 'json',
                success: function(data) {
                    var select = $('#pembimbing_id');
                    select.html('<option value="">— Pilih Pembimbing —</option>');
                    $.each(data, function(i, item) {
                        select.append('<option value="' + item.id + '">' + item.nama_pembimbing + '</option>');
                    });
                    select.prop('disabled', false);
                    // Reset anak PA
                    resetAnakPa();
                }
            });
        } else {
            $('#pembimbing_id').html('<option value="">— Pilih Wilayah & Pelayanan terlebih dahulu —</option>').prop('disabled', true);
            resetAnakPa();
        }
    }

    function resetAnakPa() {
        $('#anakPaContainer').html(
            '<p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">' +
            '<i class="fas fa-info-circle"></i> Pilih Pembimbing terlebih dahulu untuk memuat daftar anak PA.</p>'
        );
    }

    $('#wilayah_id, #pelayanan_id').on('change', loadPembimbing);

    // =========================================
    // 2. CASCADING: Pembimbing → Anak PA (Multi-Select Checkboxes)
    // =========================================
    $('#pembimbing_id').on('change', function() {
        var pembimbingId = $(this).val();

        if (pembimbingId) {
            $.ajax({
                url: '{{ route("api.get-anak-pa") }}',
                data: { pembimbing_id: pembimbingId },
                dataType: 'json',
                success: function(data) {
                    var container = $('#anakPaContainer');

                    if (data.length === 0) {
                        container.html(
                            '<p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">' +
                            '<i class="fas fa-exclamation-circle"></i> Tidak ada anak PA untuk pembimbing ini.</p>'
                        );
                        return;
                    }

                    var html = '<div class="select-all-bar">' +
                        '<label><input type="checkbox" id="selectAllAnak"> <strong>Pilih Semua</strong></label>' +
                        '<small id="anakCounter">0 dipilih</small>' +
                        '</div>';

                    $.each(data, function(i, item) {
                        html += '<label>' +
                            '<input type="checkbox" name="anak_pa_ids[]" value="' + item.id + '">' +
                            item.nama_anak +
                            '</label>';
                    });

                    container.html(html);

                    // Select All handler
                    $('#selectAllAnak').on('change', function() {
                        var checked = $(this).prop('checked');
                        container.find('input[name="anak_pa_ids[]"]').prop('checked', checked);
                        updateAnakCounter();
                    });

                    // Individual checkbox handler
                    container.on('change', 'input[name="anak_pa_ids[]"]', function() {
                        var total = container.find('input[name="anak_pa_ids[]"]').length;
                        var checked = container.find('input[name="anak_pa_ids[]"]:checked').length;
                        $('#selectAllAnak').prop('checked', total === checked);
                        updateAnakCounter();
                    });
                }
            });
        } else {
            resetAnakPa();
        }
    });

    function updateAnakCounter() {
        var count = $('input[name="anak_pa_ids[]"]:checked').length;
        $('#anakCounter').text(count + ' dipilih');
    }

    // =========================================
    // 3. DYNAMIC BUKU PA: Toggle "Lainnya" + Bab dropdown/input
    // =========================================
    $('#buku_pa_id').on('change', function() {
        var selectedVal = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var babContainer = $('#babContainer');
        var bukuLainnyaContainer = $('#bukuLainnyaContainer');

        if (selectedVal === 'lainnya') {
            bukuLainnyaContainer.slideDown(200);
            $('#buku_pa_lainnya').prop('required', true);

            babContainer.html(
                '<input type="number" name="bab" id="babInput" class="gkkd-form-control" ' +
                'placeholder="Masukkan nomor bab" min="1" required>'
            );
        } else if (selectedVal) {
            bukuLainnyaContainer.slideUp(200);
            $('#buku_pa_lainnya').prop('required', false).val('');

            var jumlahBab = parseInt(selectedOption.data('jumlah-bab'));
            var selectHtml = '<select name="bab" id="babSelect" class="gkkd-form-control" required>';
            selectHtml += '<option value="">— Pilih Bab —</option>';
            for (var i = 1; i <= jumlahBab; i++) {
                selectHtml += '<option value="' + i + '">Bab ' + i + '</option>';
            }
            selectHtml += '</select>';
            babContainer.html(selectHtml);
        } else {
            bukuLainnyaContainer.slideUp(200);
            $('#buku_pa_lainnya').prop('required', false).val('');
            babContainer.html(
                '<input type="number" name="bab" id="babInput" class="gkkd-form-control" ' +
                'placeholder="Pilih buku terlebih dahulu" min="1" required disabled>'
            );
        }
    });

    // Trigger on page load jika ada old values
    if ($('#buku_pa_id').val()) {
        $('#buku_pa_id').trigger('change');
    }
    if ($('#wilayah_id').val() && $('#pelayanan_id').val()) {
        loadPembimbing();
    }
});
</script>
@endsection
