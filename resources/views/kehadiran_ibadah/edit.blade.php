@extends('layouts.app')

@section('title', 'Edit Kehadiran Ibadah')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('kehadiran_ibadah.index') }}">Kehadiran Ibadah</a><span>/</span>Edit
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
                    <i class="fas fa-edit me-2" style="color: var(--info);"></i>Edit Kehadiran Ibadah
                </h3>
            </div>
            <div class="gkkd-card-body">
                <form action="{{ route('kehadiran_ibadah.update', $kehadiranIbadah->id) }}" method="POST" id="formKehadiranIbadah">
                    @csrf
                    @method('PUT')
                    @include('kehadiran_ibadah._form', [
                        'submitLabel' => 'Perbarui Kehadiran',
                        'isEdit' => true,
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
