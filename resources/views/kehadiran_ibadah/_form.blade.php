@php
    $isEdit = $isEdit ?? false;
    $manualName = (bool) old('is_nama_manual', $kehadiranIbadah->is_nama_manual ?? false);
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : old('id_wilayah', $kehadiranIbadah->id_wilayah ?? '');
    $selectedPelayanan = old('id_pelayanan', $kehadiranIbadah->id_pelayanan ?? '');
    $tanggalValue = old('tanggal_ibadah', optional($kehadiranIbadah->tanggal_ibadah ?? null)->format('Y-m-d') ?? date('Y-m-d'));
    $namaValue = old('nama_ibadah', $kehadiranIbadah->nama_ibadah ?? '');
@endphp

<div class="gkkd-form-group">
    <label for="tanggal_ibadah" class="gkkd-form-label">Tanggal Ibadah</label>
    <input type="date" name="tanggal_ibadah" id="tanggal_ibadah"
           class="gkkd-form-control"
           value="{{ $tanggalValue }}"
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
                    <option value="">-- Pilih Wilayah --</option>
                @endunless
                @foreach ($wilayahs as $wilayah)
                    <option value="{{ $wilayah->id }}"
                            data-name="{{ $wilayah->nama_wilayah }}"
                            {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>
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
                <option value="">-- Pilih Pelayanan --</option>
                @foreach ($pelayanans as $pelayanan)
                    <option value="{{ $pelayanan->id }}"
                            data-name="{{ $pelayanan->nama_pelayanan }}"
                            {{ (string) $selectedPelayanan === (string) $pelayanan->id ? 'selected' : '' }}>
                        {{ $pelayanan->nama_pelayanan }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="gkkd-form-group">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
        <label for="nama_ibadah" class="gkkd-form-label mb-0">Nama Ibadah</label>
        <button type="button" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd" id="toggleNamaManual">
            <i class="fas fa-pen"></i> Ibadah lainnya
        </button>
    </div>
    <input type="hidden" name="is_nama_manual" id="is_nama_manual" value="{{ $manualName ? 1 : 0 }}">
    <input type="text" name="nama_ibadah" id="nama_ibadah"
           class="gkkd-form-control"
           value="{{ $namaValue }}"
           placeholder="Nama ibadah otomatis dari Wilayah dan Pelayanan"
           {{ $manualName ? '' : 'readonly' }}>
    <small id="namaIbadahHelp" style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
        Nama otomatis: Ibadah [Pelayanan] GKKD Satelit [Wilayah].
    </small>
</div>

<hr style="border-color: var(--border); margin: 24px 0;">

<div class="attendance-section attendance-section--onsite">
    <h5 class="attendance-section__title">
        <i class="fas fa-users me-1"></i> Kehadiran Onsite
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="hadir_pria_onsite" class="gkkd-form-label">Hadir Pria Onsite</label>
                <input type="number" name="hadir_pria_onsite" id="hadir_pria_onsite"
                       class="gkkd-form-control calc-onsite"
                       value="{{ old('hadir_pria_onsite', $kehadiranIbadah->hadir_pria_onsite ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="hadir_wanita_onsite" class="gkkd-form-label">Hadir Wanita Onsite</label>
                <input type="number" name="hadir_wanita_onsite" id="hadir_wanita_onsite"
                       class="gkkd-form-control calc-onsite"
                       value="{{ old('hadir_wanita_onsite', $kehadiranIbadah->hadir_wanita_onsite ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="total_hadir_onsite" class="gkkd-form-label">Total Onsite</label>
                <input type="number" name="total_hadir_onsite" id="total_hadir_onsite"
                       class="gkkd-form-control total-readonly total-readonly--primary"
                       value="{{ old('total_hadir_onsite', $kehadiranIbadah->total_hadir_onsite ?? 0) }}"
                       readonly>
            </div>
        </div>
    </div>
</div>

<div class="attendance-section attendance-section--online">
    <h5 class="attendance-section__title">
        <i class="fas fa-video me-1"></i> Kehadiran Online
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="hadir_pria_online" class="gkkd-form-label">Hadir Pria Online</label>
                <input type="number" name="hadir_pria_online" id="hadir_pria_online"
                       class="gkkd-form-control calc-online"
                       value="{{ old('hadir_pria_online', $kehadiranIbadah->hadir_pria_online ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="hadir_wanita_online" class="gkkd-form-label">Hadir Wanita Online</label>
                <input type="number" name="hadir_wanita_online" id="hadir_wanita_online"
                       class="gkkd-form-control calc-online"
                       value="{{ old('hadir_wanita_online', $kehadiranIbadah->hadir_wanita_online ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="total_hadir_online" class="gkkd-form-label">Total Online</label>
                <input type="number" name="total_hadir_online" id="total_hadir_online"
                       class="gkkd-form-control total-readonly total-readonly--info"
                       value="{{ old('total_hadir_online', $kehadiranIbadah->total_hadir_online ?? 0) }}"
                       readonly>
            </div>
        </div>
    </div>
</div>

<div class="attendance-section attendance-section--baru">
    <h5 class="attendance-section__title">
        <i class="fas fa-user-plus me-1"></i> Jiwa Baru
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="baru_pria" class="gkkd-form-label">Baru Pria</label>
                <input type="number" name="baru_pria" id="baru_pria"
                       class="gkkd-form-control calc-baru"
                       value="{{ old('baru_pria', $kehadiranIbadah->baru_pria ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="baru_wanita" class="gkkd-form-label">Baru Wanita</label>
                <input type="number" name="baru_wanita" id="baru_wanita"
                       class="gkkd-form-control calc-baru"
                       value="{{ old('baru_wanita', $kehadiranIbadah->baru_wanita ?? 0) }}"
                       min="0" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="gkkd-form-group">
                <label for="total_baru" class="gkkd-form-label">Total Baru</label>
                <input type="number" name="total_baru" id="total_baru"
                       class="gkkd-form-control total-readonly total-readonly--success"
                       value="{{ old('total_baru', $kehadiranIbadah->total_baru ?? 0) }}"
                       readonly>
            </div>
        </div>
    </div>
</div>

<div class="grand-total-box">
    <div>
        <span class="grand-total-box__label">Grand Total</span>
        <strong id="grandTotalDisplay">{{ old('grand_total', $kehadiranIbadah->grand_total ?? 0) }}</strong>
    </div>
    <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', $kehadiranIbadah->grand_total ?? 0) }}">
</div>

<div class="d-flex gap-3 mt-4 flex-wrap">
    <button type="submit" class="btn-gkkd btn-primary-gkkd">
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('kehadiran_ibadah.index') }}" class="btn-gkkd btn-outline-gkkd">Batal</a>
</div>

@section('scripts')
@parent
<style>
    .attendance-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 20px;
        margin-bottom: 18px;
    }
    .attendance-section__title {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--primary);
    }
    .attendance-section--online .attendance-section__title { color: var(--info); }
    .attendance-section--baru .attendance-section__title { color: var(--success); }
    .total-readonly {
        color: #fff;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 800;
        border: 0;
    }
    .total-readonly:focus { box-shadow: none; }
    .total-readonly--primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); }
    .total-readonly--info { background: linear-gradient(135deg, var(--info), #60a5fa); }
    .total-readonly--success { background: linear-gradient(135deg, var(--success), #34d399); }
    .grand-total-box {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        color: #fff;
        border-radius: var(--radius);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-md);
    }
    .grand-total-box__label {
        display: block;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.72);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    #grandTotalDisplay {
        display: block;
        font-size: 2rem;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }
</style>
<script>
$(document).ready(function() {
    function selectedText(selector) {
        return $(selector).find('option:selected').data('name') || '';
    }

    function generatedNamaIbadah() {
        var wilayah = selectedText('#id_wilayah');
        var pelayanan = selectedText('#id_pelayanan');
        if (!wilayah || !pelayanan) {
            return '';
        }
        return 'Ibadah ' + pelayanan + ' GKKD Satelit ' + wilayah;
    }

    function setManualMode(enabled) {
        $('#is_nama_manual').val(enabled ? '1' : '0');
        $('#nama_ibadah').prop('readonly', !enabled);
        $('#toggleNamaManual').html(enabled
            ? '<i class="fas fa-magic"></i> Gunakan nama otomatis'
            : '<i class="fas fa-pen"></i> Ibadah lainnya'
        );
        $('#namaIbadahHelp').text(enabled
            ? 'Isi manual untuk ibadah khusus seperti Natal, Paskah, atau ibadah gabungan.'
            : 'Nama otomatis: Ibadah [Pelayanan] GKKD Satelit [Wilayah].'
        );
        if (!enabled) {
            $('#nama_ibadah').val(generatedNamaIbadah());
        } else {
            $('#nama_ibadah').focus();
        }
    }

    function refreshAutoName() {
        if ($('#is_nama_manual').val() !== '1') {
            $('#nama_ibadah').val(generatedNamaIbadah());
        }
    }

    function numberValue(selector) {
        return parseInt($(selector).val(), 10) || 0;
    }

    function animate(selector) {
        $(selector).css('transform', 'scale(1.04)');
        setTimeout(function() { $(selector).css('transform', 'scale(1)'); }, 120);
    }

    function calculateTotals() {
        var onsite = numberValue('#hadir_pria_onsite') + numberValue('#hadir_wanita_onsite');
        var online = numberValue('#hadir_pria_online') + numberValue('#hadir_wanita_online');
        var baru = numberValue('#baru_pria') + numberValue('#baru_wanita');
        var grandTotal = onsite + online + baru;

        $('#total_hadir_onsite').val(onsite);
        $('#total_hadir_online').val(online);
        $('#total_baru').val(baru);
        $('#grand_total').val(grandTotal);
        $('#grandTotalDisplay').text(grandTotal);
        animate('#grandTotalDisplay');
    }

    $('#toggleNamaManual').on('click', function() {
        setManualMode($('#is_nama_manual').val() !== '1');
    });
    $('#id_wilayah, #id_pelayanan').on('change', refreshAutoName);
    $('.calc-onsite, .calc-online, .calc-baru').on('input change', calculateTotals);

    setManualMode($('#is_nama_manual').val() === '1');
    refreshAutoName();
    calculateTotals();
});
</script>
@endsection
