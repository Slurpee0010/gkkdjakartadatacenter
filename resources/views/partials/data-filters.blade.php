@php
    $wilayahParam = $wilayahParam ?? 'wilayah_id';
    $pelayananParam = $pelayananParam ?? 'pelayanan_id';
    $searchPlaceholder = $searchPlaceholder ?? 'Cari data...';
    $title = $title ?? 'Filter Data';
    $filterIdPrefix = $filterIdPrefix ?? 'data_filter';
    $regionalLocked = auth()->user()?->isAdminWilayah() ?? false;
    $regionalWilayahId = auth()->user()?->wilayah_id;
    $selectedWilayah = $regionalLocked ? $regionalWilayahId : request($wilayahParam);
@endphp

<div class="gkkd-card fade-in mb-4">
    <div class="gkkd-card-header">
        <h3 class="gkkd-card-title"><i class="fas fa-filter me-2" style="color: var(--accent);"></i>{{ $title }}</h3>
    </div>
    <div class="gkkd-card-body">
        <form method="GET" action="{{ route($actionRoute) }}">
            <div class="row g-3">
                <div class="col-lg-2 col-md-6">
                    <div class="gkkd-form-group mb-0">
                        <label for="{{ $filterIdPrefix }}_date_from" class="gkkd-form-label">Tanggal Dari</label>
                        <input type="date" name="date_from" id="{{ $filterIdPrefix }}_date_from" class="gkkd-form-control"
                               value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="gkkd-form-group mb-0">
                        <label for="{{ $filterIdPrefix }}_date_to" class="gkkd-form-label">Tanggal Sampai</label>
                        <input type="date" name="date_to" id="{{ $filterIdPrefix }}_date_to" class="gkkd-form-control"
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="gkkd-form-group mb-0">
                        <label for="{{ $filterIdPrefix }}_wilayah" class="gkkd-form-label">Wilayah</label>
                        @if($regionalLocked)
                            <input type="hidden" name="{{ $wilayahParam }}" value="{{ $regionalWilayahId }}">
                        @endif
                        <select name="{{ $wilayahParam }}" id="{{ $filterIdPrefix }}_wilayah" class="gkkd-form-control" @disabled($regionalLocked)>
                            @unless($regionalLocked)
                                <option value="">Semua Wilayah</option>
                            @endunless
                            @foreach ($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id }}" {{ (string) $selectedWilayah === (string) $wilayah->id ? 'selected' : '' }}>
                                    {{ $wilayah->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="gkkd-form-group mb-0">
                        <label for="{{ $filterIdPrefix }}_pelayanan" class="gkkd-form-label">Pelayanan</label>
                        <select name="{{ $pelayananParam }}" id="{{ $filterIdPrefix }}_pelayanan" class="gkkd-form-control">
                            <option value="">Semua Pelayanan</option>
                            @foreach ($pelayanans as $pelayanan)
                                <option value="{{ $pelayanan->id }}" {{ request($pelayananParam) == $pelayanan->id ? 'selected' : '' }}>
                                    {{ $pelayanan->nama_pelayanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-12">
                    <div class="gkkd-form-group mb-0">
                        <label for="{{ $filterIdPrefix }}_search" class="gkkd-form-label">Search</label>
                        <input type="search" name="search" id="{{ $filterIdPrefix }}_search" class="gkkd-form-control"
                               value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4 flex-wrap">
                <button type="submit" class="btn-gkkd btn-primary-gkkd">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <button type="submit" name="format" value="excel" formaction="{{ route($exportRoute) }}" class="btn-gkkd btn-accent-gkkd" style="background: linear-gradient(135deg, #059669, #34d399);">
                    <i class="fas fa-file-excel"></i> Download Excel
                </button>
                <button type="submit" name="format" value="csv" formaction="{{ route($exportRoute) }}" class="btn-gkkd btn-outline-gkkd">
                    <i class="fas fa-file-csv"></i> Download CSV
                </button>
                <a href="{{ route($resetRoute) }}" class="btn-gkkd btn-outline-gkkd">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>
