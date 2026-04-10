<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Alat;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run()
    {
        $peminjam = User::where('role', 'peminjam')->first();
        $alat = Alat::first();

        if (!$peminjam || !$alat) {
            $this->command->error("Data user (role peminjam) atau data alat tidak ditemukan!");
            return;
        }

        $statuses = ['pending', 'disetujui', 'selesai', 'ditolak'];
        $daftarTujuan = ['Praktikum Olahraga', 'Latihan Mandiri', 'Turnamen Internal', 'Kegiatan UKM'];

        for ($i = 0; $i < 150; $i++) {
            // Random tanggal dalam 6 bulan terakhir
            $randomDate = Carbon::now()->subDays(rand(0, 180));
            $tglPinjam = $randomDate->copy();
            $tglKembali = $randomDate->copy()->addDays(rand(1, 3));

            Peminjaman::create([
                'user_id'    => $peminjam->id,
                'alat_id'    => $alat->id,
                'jumlah'     => rand(1, 5),
                'tujuan'     => $daftarTujuan[array_rand($daftarTujuan)], // Mengisi kolom 'tujuan'
                'tgl_pinjam' => $tglPinjam->format('Y-m-d'),
                'tgl_kembali'=> $tglKembali->format('Y-m-d'),
                'status'     => $statuses[array_rand($statuses)],
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }

        $this->command->info("Berhasil membuat 150 data peminjaman dengan kolom tujuan terisi!");
    }
}