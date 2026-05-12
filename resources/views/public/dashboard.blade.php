@extends('layouts.public')

@section('title', 'Dashboard Utama')

@section('styles')
<style>
    .choice-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
        margin-top: 24px;
    }
    .choice-card {
        padding: 24px;
        min-height: 250px;
        position: relative;
        overflow: hidden;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .choice-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 42px rgba(20,33,52,0.12);
    }
    .choice-icon {
        width: 54px;
        height: 54px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        margin-bottom: 18px;
    }
    .choice-card h2 { font-size: 1.35rem; font-weight: 900; margin-bottom: 8px; }
    .choice-card p { color: var(--muted); line-height: 1.6; margin-bottom: 20px; }
    .quick-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-top: 22px;
    }
    .quick-step {
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #fff;
        color: var(--muted);
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<section class="public-card" style="padding: 28px;">
    <div style="max-width: 760px;">
        <span style="display: inline-flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 900; margin-bottom: 10px;">
            <i class="fas fa-bolt"></i> Dashboard Utama
        </span>
        <h1 style="font-size: clamp(2rem, 4vw, 3.5rem); line-height: 1.05; font-weight: 950; margin: 0 0 14px;">Pilih laporan yang mau dikirim.</h1>
        <p style="font-size: 1.03rem; color: var(--muted); line-height: 1.7; margin: 0;">Area publik ini hanya untuk input laporan. Tidak ada data laporan yang ditampilkan di halaman guest.</p>
    </div>

    <div class="quick-steps">
        <div class="quick-step"><i class="fas fa-map-marker-alt"></i> Pilih wilayah</div>
        <div class="quick-step"><i class="fas fa-users"></i> Pilih pelayanan</div>
        <div class="quick-step"><i class="fas fa-check-circle"></i> Kirim laporan</div>
    </div>
</section>

<section class="choice-grid">
    <article class="public-card choice-card">
        <div class="choice-icon" style="background: rgba(22,57,95,0.09); color: var(--primary);">
            <i class="fas fa-book-open"></i>
        </div>
        <h2>Laporan PA</h2>
        <p>Input aktivitas PA kelompok, pilih pembimbing, anak PA, buku, bab, dan tanggal pelaksanaan.</p>
        <a href="{{ route('public.laporan-pa') }}" class="public-btn public-btn-primary">
            Mulai Laporan PA <i class="fas fa-arrow-right"></i>
        </a>
    </article>

    <article class="public-card choice-card">
        <div class="choice-icon" style="background: rgba(232,168,56,0.16); color: #9a6508;">
            <i class="fas fa-church"></i>
        </div>
        <h2>Laporan Blesscomn</h2>
        <p>Input kehadiran Blesscomn, jumlah hadir pria/wanita, dan jiwa baru dalam satu form ringkas.</p>
        <a href="{{ route('public.laporan-blesscomn') }}" class="public-btn public-btn-accent">
            Mulai Blesscomn <i class="fas fa-arrow-right"></i>
        </a>
    </article>
</section>
@endsection
