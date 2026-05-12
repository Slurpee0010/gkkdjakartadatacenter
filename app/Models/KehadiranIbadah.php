<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KehadiranIbadah extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kehadiran_ibadah';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_wilayah',
        'id_pelayanan',
        'nama_ibadah',
        'is_nama_manual',
        'tanggal_ibadah',
        'hadir_pria_onsite',
        'hadir_wanita_onsite',
        'total_hadir_onsite',
        'hadir_pria_online',
        'hadir_wanita_online',
        'total_hadir_online',
        'baru_pria',
        'baru_wanita',
        'total_baru',
        'grand_total',
    ];

    protected $casts = [
        'tanggal_ibadah' => 'date',
        'is_nama_manual' => 'boolean',
    ];

    public static function buildNamaIbadah(Wilayah $wilayah, Pelayanan $pelayanan): string
    {
        return 'Ibadah ' . $pelayanan->nama_pelayanan . ' GKKD Satelit ' . $wilayah->nama_wilayah;
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class, 'id_pelayanan');
    }
}
