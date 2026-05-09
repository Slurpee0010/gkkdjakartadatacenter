<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnakBimbingan extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi massal
    protected $fillable = ['nama_anak', 'pembimbing_id', 'wilayah_id', 'pelayanan_id'];

    // Relasi dengan model Pembimbing
    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }

    // Relasi dengan model Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    // Relasi dengan model Pelayanan
    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class);
    }
}
