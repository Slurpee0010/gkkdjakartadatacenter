@extends('layouts.public')

@section('title', 'Laporan PA')

@section('content')
<div class="public-card" style="padding: 26px;">
    <div style="display: flex; justify-content: space-between; gap: 18px; flex-wrap: wrap; margin-bottom: 22px;">
        <div>
            <h1 style="font-size: 1.7rem; font-weight: 950; margin-bottom: 6px;">Form Laporan PA</h1>
            <p style="color: var(--muted); margin: 0;">Isi aktivitas PA kelompok dan pilih anak PA yang hadir.</p>
        </div>
        <a href="{{ route('public.dashboard') }}" class="public-btn" style="border: 1px solid var(--border); color: var(--primary); background: #fff;">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <form method="POST" action="{{ route('public.laporan-pa.store') }}" id="publicPaForm">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="public-label" for="tanggal_pa">Tanggal PA</label>
                <input type="date" name="tanggal_pa" id="tanggal_pa" class="public-form-control" value="{{ old('tanggal_pa', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="public-label" for="wilayah_id">Wilayah</label>
                <select name="wilayah_id" id="wilayah_id" class="public-form-control" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach($wilayahs as $wilayah)
                        <option value="{{ $wilayah->id }}" @selected(old('wilayah_id') == $wilayah->id)>{{ $wilayah->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="public-label" for="pelayanan_id">Pelayanan</label>
                <select name="pelayanan_id" id="pelayanan_id" class="public-form-control" required>
                    <option value="">Pilih Pelayanan</option>
                    @foreach($pelayanans as $pelayanan)
                        <option value="{{ $pelayanan->id }}" @selected(old('pelayanan_id') == $pelayanan->id)>{{ $pelayanan->nama_pelayanan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="public-label" for="pembimbing_id">Pembimbing</label>
            <select name="pembimbing_id" id="pembimbing_id" class="public-form-control" required disabled>
                <option value="">Pilih wilayah dan pelayanan terlebih dahulu</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="public-label">Anak PA</label>
            <div id="anakPaContainer" style="border: 1px solid var(--border); border-radius: 8px; padding: 14px; min-height: 54px; background: #fff;">
                <span style="color: var(--muted);">Pilih pembimbing terlebih dahulu.</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="public-label" for="buku_pa_id">Buku PA</label>
                <select name="buku_pa_id" id="buku_pa_id" class="public-form-control">
                    <option value="">Pilih Buku PA</option>
                    @foreach($bukuPas as $buku)
                        <option value="{{ $buku->id }}" data-jumlah-bab="{{ $buku->jumlah_bab }}" @selected(old('buku_pa_id') == $buku->id)>{{ $buku->nama_buku }} ({{ $buku->jumlah_bab }} bab)</option>
                    @endforeach
                    <option value="lainnya" @selected(old('buku_pa_id') === 'lainnya')>Lainnya</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="public-label" for="babInput">Bab</label>
                <div id="babContainer">
                    <input type="number" name="bab" id="babInput" class="public-form-control" min="1" placeholder="Pilih buku terlebih dahulu" required disabled>
                </div>
            </div>
        </div>

        <div class="mb-3" id="bukuLainnyaContainer" style="display: none;">
            <label class="public-label" for="buku_pa_lainnya">Nama Buku PA Lainnya</label>
            <input type="text" name="buku_pa_lainnya" id="buku_pa_lainnya" class="public-form-control" value="{{ old('buku_pa_lainnya') }}" maxlength="255">
        </div>

        <button type="submit" class="public-btn public-btn-primary">
            <i class="fas fa-save"></i> Kirim Laporan PA
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    function resetAnak() {
        $('#anakPaContainer').html('<span style="color: var(--muted);">Pilih pembimbing terlebih dahulu.</span>');
    }
    function loadPembimbing() {
        const wilayahId = $('#wilayah_id').val();
        const pelayananId = $('#pelayanan_id').val();
        const select = $('#pembimbing_id');
        if (!wilayahId || !pelayananId) {
            select.html('<option value="">Pilih wilayah dan pelayanan terlebih dahulu</option>').prop('disabled', true);
            resetAnak();
            return;
        }
        $.getJSON('{{ route('public.options.pembimbing') }}', { wilayah_id: wilayahId, pelayanan_id: pelayananId }, function(data) {
            select.html('<option value="">Pilih Pembimbing</option>');
            data.forEach(item => select.append(`<option value="${item.id}">${item.nama_pembimbing}</option>`));
            if (data.length === 0) select.html('<option value="">Tidak ada pembimbing untuk filter ini</option>');
            select.prop('disabled', data.length === 0);
            resetAnak();
        });
    }
    $('#wilayah_id, #pelayanan_id').on('change', loadPembimbing);
    $('#pembimbing_id').on('change', function() {
        const id = this.value;
        if (!id) { resetAnak(); return; }
        $.getJSON('{{ route('public.options.anak-pa') }}', { pembimbing_id: id }, function(data) {
            if (data.length === 0) {
                $('#anakPaContainer').html('<span style="color: var(--muted);">Tidak ada anak PA untuk pembimbing ini.</span>');
                return;
            }
            let html = '<label style="display:block; margin-bottom:10px;"><input type="checkbox" id="selectAllAnak"> <strong>Pilih Semua</strong></label>';
            data.forEach(item => {
                html += `<label style="display:block; padding:7px 0;"><input type="checkbox" name="anak_pa_ids[]" value="${item.id}"> ${item.nama_anak}</label>`;
            });
            $('#anakPaContainer').html(html);
            $('#selectAllAnak').on('change', function() {
                $('input[name="anak_pa_ids[]"]').prop('checked', this.checked);
            });
        });
    });
    $('#buku_pa_id').on('change', function() {
        const val = this.value;
        const jumlahBab = parseInt($(this).find(':selected').data('jumlah-bab')) || 0;
        if (val === 'lainnya') {
            $('#bukuLainnyaContainer').slideDown(150);
            $('#buku_pa_lainnya').prop('required', true);
            $('#babContainer').html('<input type="number" name="bab" class="public-form-control" min="1" required>');
            return;
        }
        $('#bukuLainnyaContainer').slideUp(150);
        $('#buku_pa_lainnya').prop('required', false).val('');
        if (!val) {
            $('#babContainer').html('<input type="number" name="bab" class="public-form-control" min="1" placeholder="Pilih buku terlebih dahulu" required disabled>');
            return;
        }
        let options = '<select name="bab" class="public-form-control" required><option value="">Pilih Bab</option>';
        for (let i = 1; i <= jumlahBab; i++) options += `<option value="${i}">Bab ${i}</option>`;
        options += '</select>';
        $('#babContainer').html(options);
    });
});
</script>
@endsection
