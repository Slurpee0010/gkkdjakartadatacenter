<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
  use HasFactory;

    // Kolom yang dapat diisi massal
    protected $fillable = ['nama_wilayah'];
}
