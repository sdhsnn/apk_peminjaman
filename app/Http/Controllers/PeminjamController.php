<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::query();

        if ($request->has('kategori') && $request->kategori != 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('search')) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%');
        }

        $alats = $query->get();
        $categories = Alat::distinct()->pluck('kategori');
        return view('peminjam.dashboard', compact('alats', 'categories'));
    }

    /* ======================
       PROSES PEMINJAMAN
       ====================== */
    public function ajukanPeminjaman($id)
    {
        $alat = Alat::findOrFail($id);
        return view('peminjam.ajukan', compact('alat'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'id_alat'     => 'required|exists:alats,id',
            'jumlah'      => 'required|integer|min:1',
            'tgl_pinjam'  => 'required|date|after_or_equal:today',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
            'tujuan'      => 'required|string|max:255',
        ]);

        // HITUNG DURASI
        $tglPinjam = Carbon::parse($request->tgl_pinjam);
        $tglKembali = Carbon::parse($request->tgl_kembali);
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > 3) {
            return back()->with('error', 'Gagal! Batas maksimal peminjaman adalah 3 hari.');
        }

        $alat = Alat::find($request->id_alat);
        if ($request->jumlah > $alat->stok_tersedia) {
            return back()->with('error', 'Maaf, stok tidak mencukupi.');
        }

        Peminjaman::create([
            'user_id'     => Auth::id(),
            'alat_id'     => $request->id_alat,
            'jumlah'      => $request->jumlah,
            'tgl_pinjam'  => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'tujuan'      => $request->tujuan,
            'status'      => 'pending',
        ]);

        return redirect()->route('peminjam.dashboard')->with('success', 'Permintaan pinjam berhasil dikirim!');
    }

    /* ======================
       PROSES PENGEMBALIAN
       ====================== */
    public function kembali()
    {
        $peminjamans = Peminjaman::with('alat')
            ->where('user_id', Auth::id())
            ->where('status', 'disetujui') 
            ->get();

        return view('peminjam.kembali', compact('peminjamans'));
    }

    public function prosesKembali($id)
    {
        $pinjam = Peminjaman::findOrFail($id);
        
        // 1. Definisikan nilai awal (default) di luar IF
        $total_denda = 0; 
        $hariIni = now()->startOfDay();
        $deadline = \Carbon\Carbon::parse($pinjam->tgl_kembali)->startOfDay();

        // 2. Hitung denda jika terlambat
        if ($hariIni->gt($deadline)) {
            // Gunakan (int) agar tidak muncul angka keriting lagi
            $selisihHari = (int) $hariIni->diffInDays($deadline);
            $total_denda = $selisihHari * 5000;
        }

        // 3. Sekarang $total_denda aman digunakan karena sudah didefinisikan di atas
        $pinjam->update([
            'status' => 'dikembalikan',
            'tgl_dikembalikan' => now(),
            'total_denda' => $total_denda
        ]);

        return redirect()->back()->with('success', 'Alat berhasil dikembalikan! Denda: Rp ' . number_format($total_denda, 0, ',', '.'));
    }
}
