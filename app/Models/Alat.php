<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $fillable = [
        'nama_alat',
        'slug',
        'kategori',
        'deskripsi',
        'stok_total',
        'stok_tersedia',
        'foto',
        'harga_sewa',
        'harga_asli',
        'kondisi'
    ];
}
