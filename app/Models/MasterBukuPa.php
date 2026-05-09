<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBukuPa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_buku_pas';

    protected $fillable = ['nama_buku', 'jumlah_bab'];

    // Relasi ke laporan PA
    public function laporanPa()
    {
        return $this->hasMany(LaporanPa::class, 'buku_pa_id');
    }
}
