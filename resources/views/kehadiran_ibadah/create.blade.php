@extends('layouts.app')

@section('title', 'Input Kehadiran Ibadah')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('kehadiran_ibadah.index') }}">Kehadiran Ibadah</a><span>/</span>Input
@endsection

@section('content')
<a href="{{ route('kehadiran_ibadah.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kehadiran
</a>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="gkkd-card fade-in">
            <div class="gkkd-card-header">
                <h3 class="gkkd-card-title">
                    <i class="fas fa-plus-circle me-2" style="color: var(--accent);"></i>Input Kehadiran Ibadah
                </h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('kehadiran_ibadah.store') }}" method="POST" id="formKehadiranIbadah">
                    @csrf
                    @include('kehadiran_ibadah._form', [
                        'submitLabel' => 'Simpan Kehadiran',
                        'isEdit' => false,
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
