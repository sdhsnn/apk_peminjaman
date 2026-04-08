<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alat;
use Illuminate\Support\Str;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $alats = [
            [
                'nama_alat' => 'Bola Voli Mikasa V300W',
                'kategori' => 'Bola',
                'stok_total' => 10,
                'harga_sewa' => 15000,
                'harga_asli' => 85000,
                'kondisi' => 'Baik',
                'deskripsi' => 'Bola voli profesional merek Mikasa',
            ],
            [
                'nama_alat' => 'Raket Badminton Yonex Arcsaber',
                'kategori' => 'Raket',
                'stok_total' => 15,
                'harga_sewa' => 20000,
                'harga_asli' => 120000,
                'kondisi' => 'Lecet',
                'deskripsi' => 'Raket badminton Yonex asli',
            ],
        ];

        foreach ($alats as $item) {
            Alat::create([
                'nama_alat' => $item['nama_alat'],
                'slug' => Str::slug($item['nama_alat']) . '-' . Str::random(5),
                'kategori' => $item['kategori'],
                'stok_total' => $item['stok_total'],
                'stok_tersedia' => $item['stok_total'],
                'harga_sewa' => $item['harga_sewa'],
                'harga_asli' => $item['harga_asli'],
                'kondisi' => $item['kondisi'],
                'deskripsi' => $item['deskripsi'],
                'foto' => null,
            ]);
        }
    }
}