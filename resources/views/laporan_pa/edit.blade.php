@extends('layouts.app')

@section('title', 'Edit Laporan PA')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('laporan_pa.index') }}">Laporan PA</a><span>/</span>Edit
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : old('wilayah_id', $laporanPa->wilayah_id);
@endphp
<a href="{{ route('laporan_pa.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Laporan PA
</a>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-edit me-2" style="color: var(--info);"></i>Edit Laporan PA</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('laporan_pa.update', $laporanPa->id) }}" method="POST" id="formLaporanPa">
                    @csrf
                    @method('PUT')

                    {{-- Tanggal PA --}}
                    <div class="gkkd-form-group">
                        <label for="tanggal_pa" class="gkkd-form-label">Tanggal PA</label>
                        <input type="date" name="tanggal_pa" id="tanggal_pa" class="gkkd-form-control"
                               value="{{ old('tanggal_pa', $laporanPa->tanggal_pa->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Wilayah & Pelayanan --}}
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
                                        <option value="{{ $wilayah->id }}" {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>
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
                                        <option value="{{ $pelayanan->id }}" {{ old('pelayanan_id', $laporanPa->pelayanan_id) == $pelayanan->id ? 'selected' : '' }}>
                                            {{ $pelayanan->nama_pelayanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Pembimbing --}}
                    <div class="gkkd-form-group">
                        <label for="pembimbing_id" class="gkkd-form-label">Pembimbing</label>
                        <select name="pembimbing_id" id="pembimbing_id" class="gkkd-form-control" required>
                            <option value="">— Pilih Pembimbing —</option>
                            @foreach ($pembimbings as $pembimbing)
                                <option value="{{ $pembimbing->id }}" {{ old('pembimbing_id', $laporanPa->pembimbing_id) == $pembimbing->id ? 'selected' : '' }}>
                                    {{ $pembimbing->nama_pembimbing }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Daftar pembimbing difilter berdasarkan Wilayah & Pelayanan yang dipilih.
                        </small>
                    </div>

                    {{-- Anak PA (single select for edit — edit satu row) --}}
                    <div class="gkkd-form-group">
                        <label for="anak_pa_id" class="gkkd-form-label">Anak PA</label>
                        <select name="anak_pa_id" id="anak_pa_id" class="gkkd-form-control" required>
                            <option value="">— Pilih Anak PA —</option>
                            @foreach ($anakPas as $anak)
                                <option value="{{ $anak->id }}" {{ old('anak_pa_id', $laporanPa->anak_pa_id) == $anak->id ? 'selected' : '' }}>
                                    {{ $anak->nama_anak }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Daftar anak PA difilter berdasarkan Pembimbing yang dipilih.
                        </small>
                    </div>

                    <hr style="border-color: var(--border); margin: 24px 0;">

                    {{-- Buku PA — Ticket 1: tidak pakai required --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="buku_pa_id" class="gkkd-form-label">Buku PA</label>
                                <select name="buku_pa_id" id="buku_pa_id" class="gkkd-form-control">
                                    <option value="">— Pilih Buku PA —</option>
                                    @foreach ($bukuPas as $buku)
                                        <option value="{{ $buku->id }}"
                                                data-jumlah-bab="{{ $buku->jumlah_bab }}"
                                                {{ old('buku_pa_id', $laporanPa->buku_pa_id) == $buku->id ? 'selected' : '' }}>
                                            {{ $buku->nama_buku }} ({{ $buku->jumlah_bab }} bab)
                                        </option>
                                    @endforeach
                                    <option value="lainnya" {{ (!$laporanPa->buku_pa_id && $laporanPa->buku_pa_lainnya) ? 'selected' : (old('buku_pa_id') == 'lainnya' ? 'selected' : '') }}>
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
                                           value="{{ old('bab', $laporanPa->bab) }}" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Buku Lainnya --}}
                    <div class="gkkd-form-group" id="bukuLainnyaContainer" style="{{ (!$laporanPa->buku_pa_id && $laporanPa->buku_pa_lainnya) ? '' : 'display: none;' }}">
                        <label for="buku_pa_lainnya" class="gkkd-form-label">Nama Buku PA (Lainnya)</label>
                        <input type="text" name="buku_pa_lainnya" id="buku_pa_lainnya" class="gkkd-form-control"
                               placeholder="Tulis nama buku PA..." value="{{ old('buku_pa_lainnya', $laporanPa->buku_pa_lainnya) }}">
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd">
                            <i class="fas fa-save"></i> Update Laporan
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
<script>
$(document).ready(function() {
    var currentBab = {{ old('bab', $laporanPa->bab) }};

    // =========================================
    // 1. CASCADING: Wilayah/Pelayanan → Pembimbing
    // =========================================
    function loadPembimbing(preserveValue) {
        var wilayahId = $('#wilayah_id').val();
        var pelayananId = $('#pelayanan_id').val();
        var currentPembimbingId = preserveValue || '';

        if (wilayahId && pelayananId) {
            $.ajax({
                url: '{{ route("api.get-pembimbing") }}',
                data: { wilayah_id: wilayahId, pelayanan_id: pelayananId },
                dataType: 'json',
                success: function(data) {
                    var select = $('#pembimbing_id');
                    select.html('<option value="">— Pilih Pembimbing —</option>');
                    $.each(data, function(i, item) {
                        var selected = (item.id == currentPembimbingId) ? ' selected' : '';
                        select.append('<option value="' + item.id + '"' + selected + '>' + item.nama_pembimbing + '</option>');
                    });
                    select.prop('disabled', false);

                    if (!currentPembimbingId) {
                        $('#anak_pa_id').html('<option value="">— Pilih Pembimbing terlebih dahulu —</option>').prop('disabled', true);
                    }
                }
            });
        }
    }

    $('#wilayah_id, #pelayanan_id').on('change', function() {
        loadPembimbing();
        $('#anak_pa_id').html('<option value="">— Pilih Pembimbing terlebih dahulu —</option>').prop('disabled', true);
    });

    // =========================================
    // 2. CASCADING: Pembimbing → Anak PA
    // =========================================
    function loadAnakPa(preserveValue) {
        var pembimbingId = $('#pembimbing_id').val();
        var currentAnakId = preserveValue || '';

        if (pembimbingId) {
            $.ajax({
                url: '{{ route("api.get-anak-pa") }}',
                data: { pembimbing_id: pembimbingId },
                dataType: 'json',
                success: function(data) {
                    var select = $('#anak_pa_id');
                    select.html('<option value="">— Pilih Anak PA —</option>');
                    $.each(data, function(i, item) {
                        var selected = (item.id == currentAnakId) ? ' selected' : '';
                        select.append('<option value="' + item.id + '"' + selected + '>' + item.nama_anak + '</option>');
                    });
                    select.prop('disabled', false);
                }
            });
        }
    }

    $('#pembimbing_id').on('change', function() {
        loadAnakPa();
    });

    // =========================================
    // 3. DYNAMIC BUKU PA
    // =========================================
    function handleBukuChange(preserveBab) {
        var selectedVal = $('#buku_pa_id').val();
        var selectedOption = $('#buku_pa_id').find('option:selected');
        var babContainer = $('#babContainer');
        var bukuLainnyaContainer = $('#bukuLainnyaContainer');
        var babValue = preserveBab || '';

        if (selectedVal === 'lainnya') {
            bukuLainnyaContainer.slideDown(200);
            $('#buku_pa_lainnya').prop('required', true);
            babContainer.html(
                '<input type="number" name="bab" id="babInput" class="gkkd-form-control" ' +
                'placeholder="Masukkan nomor bab" min="1" value="' + babValue + '" required>'
            );
        } else if (selectedVal) {
            bukuLainnyaContainer.slideUp(200);
            $('#buku_pa_lainnya').prop('required', false);

            var jumlahBab = parseInt(selectedOption.data('jumlah-bab'));
            var selectHtml = '<select name="bab" id="babSelect" class="gkkd-form-control" required>';
            selectHtml += '<option value="">— Pilih Bab —</option>';
            for (var i = 1; i <= jumlahBab; i++) {
                var selected = (i == babValue) ? ' selected' : '';
                selectHtml += '<option value="' + i + '"' + selected + '>Bab ' + i + '</option>';
            }
            selectHtml += '</select>';
            babContainer.html(selectHtml);
        } else {
            bukuLainnyaContainer.slideUp(200);
            $('#buku_pa_lainnya').prop('required', false);
            babContainer.html(
                '<input type="number" name="bab" id="babInput" class="gkkd-form-control" ' +
                'placeholder="Pilih buku terlebih dahulu" min="1" required disabled>'
            );
        }
    }

    $('#buku_pa_id').on('change', function() {
        handleBukuChange();
    });

    // Trigger on load with preserved values
    if ($('#buku_pa_id').val()) {
        handleBukuChange(currentBab);
    }
});
</script>
@endsection
