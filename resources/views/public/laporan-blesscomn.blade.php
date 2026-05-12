@extends('layouts.public')

@section('title', 'Laporan Blesscomn')

@section('content')
<div class="public-card" style="padding: 26px;">
    <div style="display: flex; justify-content: space-between; gap: 18px; flex-wrap: wrap; margin-bottom: 22px;">
        <div>
            <h1 style="font-size: 1.7rem; font-weight: 950; margin-bottom: 6px;">Form Laporan Blesscomn</h1>
            <p style="color: var(--muted); margin: 0;">Isi data pelaksanaan dan kehadiran Blesscomn.</p>
        </div>
        <a href="{{ route('public.dashboard') }}" class="public-btn" style="border: 1px solid var(--border); color: var(--primary); background: #fff;">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <form method="POST" action="{{ route('public.laporan-blesscomn.store') }}" id="publicBlesscomnForm">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="public-label" for="tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" class="public-form-control" value="{{ old('tanggal_pelaksanaan', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="public-label" for="id_wilayah">Wilayah</label>
                <select name="id_wilayah" id="id_wilayah" class="public-form-control" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach($wilayahs as $wilayah)
                        <option value="{{ $wilayah->id }}" @selected(old('id_wilayah') == $wilayah->id)>{{ $wilayah->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="public-label" for="id_pelayanan">Pelayanan</label>
                <select name="id_pelayanan" id="id_pelayanan" class="public-form-control" required>
                    <option value="">Pilih Pelayanan</option>
                    @foreach($pelayanans as $pelayanan)
                        <option value="{{ $pelayanan->id }}" @selected(old('id_pelayanan') == $pelayanan->id)>{{ $pelayanan->nama_pelayanan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="public-label" for="id_blesscomn">Blesscomn</label>
            <select name="id_blesscomn" id="id_blesscomn" class="public-form-control" required disabled>
                <option value="">Pilih wilayah dan pelayanan terlebih dahulu</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="public-label" for="hadir_pria">Hadir Pria</label>
                <input type="number" name="hadir_pria" id="hadir_pria" class="public-form-control calc-hadir" value="{{ old('hadir_pria', 0) }}" min="0" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="public-label" for="hadir_wanita">Hadir Wanita</label>
                <input type="number" name="hadir_wanita" id="hadir_wanita" class="public-form-control calc-hadir" value="{{ old('hadir_wanita', 0) }}" min="0" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="public-label" for="baru_pria">Baru Pria</label>
                <input type="number" name="baru_pria" id="baru_pria" class="public-form-control calc-baru" value="{{ old('baru_pria', 0) }}" min="0" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="public-label" for="baru_wanita">Baru Wanita</label>
                <input type="number" name="baru_wanita" id="baru_wanita" class="public-form-control calc-baru" value="{{ old('baru_wanita', 0) }}" min="0" required>
            </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin: 10px 0 22px;">
            <div style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff;"><strong>Total Hadir:</strong> <span id="totalHadir">0</span></div>
            <div style="padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff;"><strong>Total Baru:</strong> <span id="totalBaru">0</span></div>
        </div>

        <button type="submit" class="public-btn public-btn-primary">
            <i class="fas fa-save"></i> Kirim Laporan Blesscomn
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    function loadBlesscomn() {
        const wId = $('#id_wilayah').val();
        const pId = $('#id_pelayanan').val();
        const select = $('#id_blesscomn');
        if (!wId || !pId) {
            select.html('<option value="">Pilih wilayah dan pelayanan terlebih dahulu</option>').prop('disabled', true);
            return;
        }
        $.getJSON('{{ route('public.options.blesscomn') }}', { id_wilayah: wId, id_pelayanan: pId }, function(data) {
            select.html('<option value="">Pilih Blesscomn</option>');
            data.forEach(item => select.append(`<option value="${item.id}">${item.nama_blesscomn}</option>`));
            if (data.length === 0) select.html('<option value="">Tidak ada Blesscomn untuk filter ini</option>');
            select.prop('disabled', data.length === 0);
        });
    }
    function calc() {
        let hadir = 0, baru = 0;
        $('.calc-hadir').each(function(){ hadir += parseInt(this.value) || 0; });
        $('.calc-baru').each(function(){ baru += parseInt(this.value) || 0; });
        $('#totalHadir').text(hadir);
        $('#totalBaru').text(baru);
    }
    $('#id_wilayah, #id_pelayanan').on('change', loadBlesscomn);
    $('.calc-hadir, .calc-baru').on('input change', calc);
    calc();
});
</script>
@endsection
