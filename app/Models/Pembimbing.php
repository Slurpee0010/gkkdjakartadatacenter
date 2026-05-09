<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembimbing extends Model
{
    protected $fillable = ['nama_pembimbing', 'wilayah_id', 'pelayanan_id'];  // Kolom yang boleh diisi

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);  // Pembimbing memiliki Wilayah
    }

    // Relasi ke Pelayanan
    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class);  // Pembimbing memiliki Pelayanan
    }
}
