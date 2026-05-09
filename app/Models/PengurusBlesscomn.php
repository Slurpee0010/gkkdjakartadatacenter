<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengurusBlesscomn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_ketua',
        'no_wa_ketua',
        'id_wilayah',
        'id_pelayanan',
        'nama_asisten',
        'no_wa_asisten',
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
    public function blesscomns()
    {
        return $this->hasMany(MasterBlesscomn::class, 'id_pengurus');
    }
}
