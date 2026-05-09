<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanBlesscomn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal_pelaksanaan',
        'id_wilayah',
        'id_pelayanan',
        'id_blesscomn',
        'hadir_pria',
        'hadir_wanita',
        'total_hadir',
        'baru_pria',
        'baru_wanita',
        'total_baru',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

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

    // Relasi ke Master Blesscomn
    public function blesscomn()
    {
        return $this->belongsTo(MasterBlesscomn::class, 'id_blesscomn');
    }
}
