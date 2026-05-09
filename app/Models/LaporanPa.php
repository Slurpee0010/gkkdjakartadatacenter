<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanPa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_pas';

    protected $fillable = [
        'wilayah_id',
        'pelayanan_id',
        'pembimbing_id',
        'anak_pa_id',
        'buku_pa_id',
        'buku_pa_lainnya',
        'bab',
        'tanggal_pa',
    ];

    protected $casts = [
        'tanggal_pa' => 'date',
    ];

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    // Relasi ke Pelayanan
    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class);
    }

    // Relasi ke Pembimbing
    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }

    // Relasi ke Anak PA (anak_bimbingans)
    public function anakPa()
    {
        return $this->belongsTo(AnakBimbingan::class, 'anak_pa_id');
    }

    // Relasi ke Master Buku PA
    public function bukuPa()
    {
        return $this->belongsTo(MasterBukuPa::class, 'buku_pa_id');
    }

    /**
     * Mendapatkan nama buku (dari master atau input manual).
     */
    public function getNamaBukuAttribute()
    {
        if ($this->buku_pa_id && $this->bukuPa) {
            return $this->bukuPa->nama_buku;
        }
        return $this->buku_pa_lainnya ?? '-';
    }
}
