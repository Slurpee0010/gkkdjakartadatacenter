@extends('layouts.app')

@section('title', 'Tambah Master Blesscomn')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('master_blesscomn.index') }}">Master Blesscomn</a><span>/</span>Tambah
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : old('id_wilayah');
@endphp
<style>
    .pengurus-inline-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 31, 58, 0.48);
    }

    .pengurus-inline-modal.is-open {
        display: flex;
    }

    .pengurus-inline-modal-dialog {
        width: min(760px, 100%);
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        background: var(--surface-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
    }

    .pengurus-inline-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 16px;
        border-bottom: 1px solid var(--border);
    }

    .pengurus-inline-modal-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .pengurus-inline-modal-subtitle {
        margin: 4px 0 0;
        color: var(--text-secondary);
        font-size: 0.82rem;
    }

    .pengurus-inline-modal-close {
        width: 38px;
        height: 38px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface-card);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pengurus-inline-modal-close:hover {
        border-color: var(--danger);
        color: var(--danger);
        background: rgba(239, 68, 68, 0.06);
    }

    .pengurus-inline-modal-body {
        padding: 24px;
    }

    .pengurus-inline-modal-alert {
        align-items: flex-start;
    }

    .pengurus-inline-modal-alert ul {
        margin: 0;
        padding-left: 18px;
    }

    .pengurus-field-error {
        display: none;
        margin-top: 5px;
        color: var(--danger);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .pengurus-input-error {
        border-color: var(--danger) !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08) !important;
    }

    @media (max-width: 575.98px) {
        .pengurus-inline-modal {
            padding: 14px;
            align-items: flex-start;
        }

        .pengurus-inline-modal-dialog {
            max-height: calc(100vh - 28px);
        }

        .pengurus-inline-modal-header,
        .pengurus-inline-modal-body {
            padding: 18px;
        }
    }
</style>

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
                                    <button type="button" class="btn-gkkd btn-outline-gkkd btn-sm-gkkd mt-2" id="openPengurusModal">
                                        <i class="fas fa-plus"></i> Tambah Ketua Baru
                                    </button>
                                    <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 5px; display: block;">
                                        Jika ketua belum ada, tambahkan dari sini tanpa meninggalkan form.
                                    </small>
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
                                @if($regionalLocked)
                                    <input type="hidden" name="id_wilayah" value="{{ $regionalWilayahId }}">
                                @endif
                                <select name="id_wilayah" id="id_wilayah" class="gkkd-form-control" required @disabled($regionalLocked)>
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

<div class="pengurus-inline-modal" id="pengurusInlineModal" aria-hidden="true">
    <div class="pengurus-inline-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="pengurusInlineModalTitle">
        <div class="pengurus-inline-modal-header">
            <div>
                <h4 class="pengurus-inline-modal-title" id="pengurusInlineModalTitle">Tambah Pengurus Blesscomn</h4>
                <p class="pengurus-inline-modal-subtitle">Data akan disimpan ke Pengurus Blesscomn dan langsung dipilih sebagai ketua.</p>
            </div>
            <button type="button" class="pengurus-inline-modal-close" id="closePengurusModal" aria-label="Tutup modal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="pengurus-inline-modal-body">
            <form id="formPengurusInline" autocomplete="off">
                @csrf

                <div class="gkkd-alert gkkd-alert-danger pengurus-inline-modal-alert" id="pengurusInlineError" style="display: none;"></div>

                <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                    <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                        <i class="fas fa-user-tie me-1"></i> Data Ketua
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="modal_nama_ketua" class="gkkd-form-label">Nama Ketua</label>
                                <input type="text" name="nama_ketua" id="modal_nama_ketua" class="gkkd-form-control"
                                       placeholder="Masukkan nama ketua" required>
                                <div class="pengurus-field-error" data-pengurus-error-for="nama_ketua"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="modal_no_wa_ketua" class="gkkd-form-label">No. WhatsApp Ketua</label>
                                <input type="text" name="no_wa_ketua" id="modal_no_wa_ketua" class="gkkd-form-control"
                                       placeholder="Contoh: 08123456789" required>
                                <div class="pengurus-field-error" data-pengurus-error-for="no_wa_ketua"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                    <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--info); margin-bottom: 16px;">
                        <i class="fas fa-user-friends me-1"></i> Data Asisten
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="modal_nama_asisten" class="gkkd-form-label">Nama Asisten</label>
                                <input type="text" name="nama_asisten" id="modal_nama_asisten" class="gkkd-form-control"
                                       placeholder="Masukkan nama asisten" required>
                                <div class="pengurus-field-error" data-pengurus-error-for="nama_asisten"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="modal_no_wa_asisten" class="gkkd-form-label">No. WhatsApp Asisten</label>
                                <input type="text" name="no_wa_asisten" id="modal_no_wa_asisten" class="gkkd-form-control"
                                       placeholder="Contoh: 08123456789" required>
                                <div class="pengurus-field-error" data-pengurus-error-for="no_wa_asisten"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="gkkd-form-group">
                            <label for="modal_id_wilayah" class="gkkd-form-label">Wilayah</label>
                            @if($regionalLocked)
                                <input type="hidden" name="id_wilayah" value="{{ $regionalWilayahId }}">
                            @endif
                            <select name="id_wilayah" id="modal_id_wilayah" class="gkkd-form-control" required @disabled($regionalLocked)>
                                @unless($regionalLocked)
                                    <option value="">Pilih Wilayah</option>
                                @endunless
                                @foreach ($wilayahs as $wilayah)
                                    <option value="{{ $wilayah->id }}" {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>{{ $wilayah->nama_wilayah }}</option>
                                @endforeach
                            </select>
                            <div class="pengurus-field-error" data-pengurus-error-for="id_wilayah"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="gkkd-form-group">
                            <label for="modal_id_pelayanan" class="gkkd-form-label">Pelayanan</label>
                            <select name="id_pelayanan" id="modal_id_pelayanan" class="gkkd-form-control" required>
                                <option value="">Pilih Pelayanan</option>
                                @foreach ($pelayanans as $pelayanan)
                                    <option value="{{ $pelayanan->id }}">{{ $pelayanan->nama_pelayanan }}</option>
                                @endforeach
                            </select>
                            <div class="pengurus-field-error" data-pengurus-error-for="id_pelayanan"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end mt-2">
                    <button type="button" class="btn-gkkd btn-outline-gkkd" id="cancelPengurusModal">Batal</button>
                    <button type="submit" class="btn-gkkd btn-primary-gkkd" id="savePengurusInline">
                        <i class="fas fa-save"></i> Simpan Pengurus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var pengurusStoreUrl = @json(route('pengurus_blesscomn.store'));
    var modal = $('#pengurusInlineModal');
    var modalForm = $('#formPengurusInline');

    // ====================================
    // Auto-fill Asisten saat memilih Ketua
    // ====================================
    $('#id_pengurus').on('change', function() {
        var selected = $(this).find('option:selected');
        var asisten = selected.data('asisten') || '';
        $('#display_asisten').val(asisten);
    });

    function syncModalWilayahPelayanan() {
        var wilayah = $('#id_wilayah').val();
        var pelayanan = $('#id_pelayanan').val();

        if (wilayah) {
            $('#modal_id_wilayah').val(wilayah);
        }
        if (pelayanan) {
            $('#modal_id_pelayanan').val(pelayanan);
        }
    }

    function clearModalErrors() {
        $('#pengurusInlineError').hide().empty();
        $('.pengurus-field-error').hide().empty();
        modalForm.find('.pengurus-input-error').removeClass('pengurus-input-error');
    }

    function showModalErrors(errors, fallbackMessage) {
        var alertBox = $('#pengurusInlineError');
        var wrapper = $('<div></div>');
        var list = $('<ul></ul>');
        var hasList = false;

        clearModalErrors();

        if (errors) {
            $.each(errors, function(field, messages) {
                var message = messages[0] || fallbackMessage;
                var fieldError = $('[data-pengurus-error-for="' + field + '"]');

                fieldError.text(message).show();
                modalForm.find('[name="' + field + '"]').addClass('pengurus-input-error');
                list.append($('<li></li>').text(message));
                hasList = true;
            });
        }

        if (!hasList && fallbackMessage) {
            list.append($('<li></li>').text(fallbackMessage));
            hasList = true;
        }

        if (hasList) {
            alertBox
                .empty()
                .append($('<i class="fas fa-exclamation-circle"></i>'))
                .append(wrapper.append(list))
                .show();
        }
    }

    function openPengurusModal() {
        clearModalErrors();
        syncModalWilayahPelayanan();
        modal.addClass('is-open').attr('aria-hidden', 'false');
        $('body').css('overflow', 'hidden');

        setTimeout(function() {
            $('#modal_nama_ketua').trigger('focus');
        }, 80);
    }

    function closePengurusModal() {
        modal.removeClass('is-open').attr('aria-hidden', 'true');
        $('body').css('overflow', '');
        clearModalErrors();
    }

    $('#openPengurusModal').on('click', openPengurusModal);
    $('#closePengurusModal, #cancelPengurusModal').on('click', closePengurusModal);

    modal.on('click', function(event) {
        if (event.target === this) {
            closePengurusModal();
        }
    });

    $(document).on('keydown', function(event) {
        if (event.key === 'Escape' && modal.hasClass('is-open')) {
            closePengurusModal();
        }
    });

    modalForm.on('submit', function(event) {
        event.preventDefault();
        clearModalErrors();

        var submitButton = $('#savePengurusInline');
        var originalButtonHtml = submitButton.html();

        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: pengurusStoreUrl,
            method: 'POST',
            data: modalForm.serialize(),
            dataType: 'json',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                var data = response.data;
                var option = new Option(data.nama_ketua, data.id, true, true);

                $(option)
                    .attr('data-asisten', data.nama_asisten)
                    .attr('data-wilayah', data.id_wilayah)
                    .attr('data-pelayanan', data.id_pelayanan)
                    .data('asisten', data.nama_asisten);

                $('#id_pengurus option[value="' + data.id + '"]').remove();
                $('#id_pengurus').append(option).val(data.id).trigger('change');

                if (!$('#id_wilayah').val()) {
                    $('#id_wilayah').val(data.id_wilayah);
                }
                if (!$('#id_pelayanan').val()) {
                    $('#id_pelayanan').val(data.id_pelayanan);
                }

                modalForm[0].reset();
                closePengurusModal();

                if (window.Swal) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.message || 'Pengurus baru berhasil ditambahkan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                var fallbackMessage = response.message || 'Pengurus belum bisa disimpan. Periksa kembali data yang diisi.';

                showModalErrors(response.errors, fallbackMessage);
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
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
