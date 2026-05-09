<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBlesscomn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_blesscomn',
        'tanggal_terbentuk',
        'id_pengurus',
        'id_wilayah',
        'id_pelayanan',
        'is_pembelahan',
        'id_blesscomn_induk',
    ];

    protected $casts = [
        'tanggal_terbentuk' => 'date',
        'is_pembelahan' => 'boolean',
    ];

    // Relasi ke Pengurus Blesscomn
    public function pengurus()
    {
        return $this->belongsTo(PengurusBlesscomn::class, 'id_pengurus');
    }

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    // Relasi ke Pelayanan
    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class, 'id_pelayanan');
    }

    // Relasi ke Blesscomn Induk (self-referencing)
    public function blesscomnInduk()
    {
        return $this->belongsTo(MasterBlesscomn::class, 'id_blesscomn_induk');
    }

    // Relasi ke Blesscomn Anak (pembelahan)
    public function blesscomnAnak()
    {
        return $this->hasMany(MasterBlesscomn::class, 'id_blesscomn_induk');
    }

    // Relasi ke Laporan
    public function laporans()
    {
        return $this->hasMany(LaporanBlesscomn::class, 'id_blesscomn');
    }
}
