<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $totalMember = User::where('role', 'peminjam')->count(); 
        $totalAlat = Alat::count();
        $sewaAktif = Peminjaman::where('status', 'disetujui')->count();
        $dikembalikan = Peminjaman::where('status', 'selesai')->count();
        
        $labels = [];
        $counts = [];

        // Ambil data 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('F Y');
            
            $counts[] = Peminjaman::whereYear('created_at', $month->year)
                                 ->whereMonth('created_at', $month->month)
                                 ->count();
        }

        // Teks ini yang akan tampil di atas grafik
        $chartTitle = "Analisis aktivitas penyewaan alat bulanan";

        return view('admin.dashboard', compact(
            'totalMember', 'totalAlat', 'sewaAktif', 'dikembalikan', 
            'labels', 'counts', 'chartTitle'
        ));
    }
}