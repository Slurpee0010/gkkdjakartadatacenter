@extends('layouts.app')

@section('title', 'Input Laporan Blesscomn')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('laporan_blesscomn.index') }}">Laporan Blesscomn</a><span>/</span>Input
@endsection

@section('content')
@php
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : old('id_wilayah', $laporanBlesscomn->id_wilayah);
@endphp
<a href="{{ route('laporan_blesscomn.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Laporan
</a>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title"><i class="fas fa-edit me-2" style="color: var(--info);"></i>Edit Laporan Blesscomn</h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('laporan_blesscomn.update', $laporanBlesscomn->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="gkkd-form-group">
                        <label for="tanggal_pelaksanaan" class="gkkd-form-label">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" class="gkkd-form-control"
                               value="{{ old('tanggal_pelaksanaan', $laporanBlesscomn->tanggal_pelaksanaan->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                    </div>

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
                                    @foreach ($wilayahs as $w)
                                        <option value="{{ $w->id }}" {{ (string) $selectedWilayah === (string) $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gkkd-form-group">
                                <label for="id_pelayanan" class="gkkd-form-label">Pelayanan</label>
                                <select name="id_pelayanan" id="id_pelayanan" class="gkkd-form-control" required>
                                    <option value="">— Pilih Pelayanan —</option>
                                    @foreach ($pelayanans as $p)
                                        <option value="{{ $p->id }}" {{ old('id_pelayanan', $laporanBlesscomn->id_pelayanan) == $p->id ? 'selected' : '' }}>{{ $p->nama_pelayanan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="gkkd-form-group">
                        <label for="id_blesscomn" class="gkkd-form-label">Blesscomn</label>
                        <select name="id_blesscomn" id="id_blesscomn" class="gkkd-form-control" required>
                            @foreach ($blesscomnList as $bc)
                                <option value="{{ $bc->id }}" {{ old('id_blesscomn', $laporanBlesscomn->id_blesscomn) == $bc->id ? 'selected' : '' }}>{{ $bc->nama_blesscomn }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr style="border-color: var(--border); margin: 24px 0;">

                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 16px;"><i class="fas fa-users me-1"></i> Data Kehadiran</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="hadir_pria" class="gkkd-form-label">Hadir Pria</label>
                                    <input type="number" name="hadir_pria" id="hadir_pria" class="gkkd-form-control calc-hadir" value="{{ old('hadir_pria', $laporanBlesscomn->hadir_pria) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="hadir_wanita" class="gkkd-form-label">Hadir Wanita</label>
                                    <input type="number" name="hadir_wanita" id="hadir_wanita" class="gkkd-form-control calc-hadir" value="{{ old('hadir_wanita', $laporanBlesscomn->hadir_wanita) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label class="gkkd-form-label">Total Hadir</label>
                                    <div id="totalHadirDisplay" style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 1.3rem; font-weight: 800; text-align: center;">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background: var(--surface); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px;">
                        <h5 style="font-size: 0.9rem; font-weight: 700; color: var(--success); margin-bottom: 16px;"><i class="fas fa-user-plus me-1"></i> Jiwa Baru</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="baru_pria" class="gkkd-form-label">Baru Pria</label>
                                    <input type="number" name="baru_pria" id="baru_pria" class="gkkd-form-control calc-baru" value="{{ old('baru_pria', $laporanBlesscomn->baru_pria) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label for="baru_wanita" class="gkkd-form-label">Baru Wanita</label>
                                    <input type="number" name="baru_wanita" id="baru_wanita" class="gkkd-form-control calc-baru" value="{{ old('baru_wanita', $laporanBlesscomn->baru_wanita) }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="gkkd-form-group">
                                    <label class="gkkd-form-label">Total Baru</label>
                                    <div id="totalBaruDisplay" style="background: linear-gradient(135deg, var(--success), #34d399); color: #fff; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 1.3rem; font-weight: 800; text-align: center;">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-gkkd btn-primary-gkkd"><i class="fas fa-save"></i> Perbarui</button>
                        <a href="{{ route('laporan_blesscomn.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
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
    function loadBlesscomn() {
        var wId = $('#id_wilayah').val(), pId = $('#id_pelayanan').val();
        if (wId && pId) {
            var currentVal = '{{ old("id_blesscomn", $laporanBlesscomn->id_blesscomn) }}';
            $.getJSON('{{ route("api.get-blesscomn") }}', {id_wilayah: wId, id_pelayanan: pId}, function(data) {
                var s = $('#id_blesscomn'); s.html('<option value="">— Pilih Blesscomn —</option>');
                $.each(data, function(i, item) {
                    var sel = item.id == currentVal ? ' selected' : '';
                    s.append('<option value="'+item.id+'"'+sel+'>'+item.nama_blesscomn+'</option>');
                });
                s.prop('disabled', false);
            });
        }
    }
    $('#id_wilayah, #id_pelayanan').on('change', loadBlesscomn);

    function calcTotal(cls, disp) {
        var t = 0; $(cls).each(function(){ t += parseInt($(this).val()) || 0; }); $(disp).text(t);
    }
    $('.calc-hadir').on('input change', function(){ calcTotal('.calc-hadir','#totalHadirDisplay'); });
    $('.calc-baru').on('input change', function(){ calcTotal('.calc-baru','#totalBaruDisplay'); });
    calcTotal('.calc-hadir','#totalHadirDisplay');
    calcTotal('.calc-baru','#totalBaruDisplay');
});
</script>
@endsection
