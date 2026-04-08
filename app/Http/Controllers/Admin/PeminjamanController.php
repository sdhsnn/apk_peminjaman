<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /* ======================
       CRUD PEMINJAMAN
       ====================== */
    public function peminjaman()
    {
        $users = User::where('role', 'peminjam')->orderBy('name')->get();

        $alats = Alat::where('stok_tersedia', '>', 0)->get();

        $peminjamanBerlangsung = Peminjaman::with(['user', 'alat'])->latest()->paginate(10);

        return view('admin.peminjaman', compact('users', 'alats', 'peminjamanBerlangsung'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'alat_id' => 'required|exists:alats,id',
            'jumlah'  => 'required|integer|min:1',
            'tgl_kembali' => 'required|date|after_or_equal:today',
        ]);

        $tglPinjam = now()->startOfDay(); 
        $tglKembali = \Carbon\Carbon::parse($request->tgl_kembali)->startOfDay();
        
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > 3) {
            return back()->with('error', "Batas maksimal peminjaman adalah 3 hari! (Input Anda: $durasi hari)");
        }

        $alat = Alat::findOrFail($request->alat_id);

        if ($alat->stok_tersedia < $request->jumlah) {
            return back()->with('error', 'Stok alat tidak mencukupi!');
        }

        Peminjaman::create([
            'user_id'     => $request->user_id,
            'alat_id'     => $request->alat_id,
            'jumlah'      => $request->jumlah,
            'tgl_pinjam'  => now(),
            'tgl_kembali' => $request->tgl_kembali,
            'status'      => 'pending',
        ]);
        
        return redirect()->back()->with('success', 'Permintaan peminjaman terkirim. Menunggu persetujuan petugas.');
    }

    public function updatePeminjaman(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);
        $alat = Alat::findOrFail($pinjam->alat_id);
        $alat->increment('stok_tersedia', $pinjam->jumlah);

        if ($alat->fresh()->stok_tersedia < $request->jumlah) {
            $alat->decrement('stok_tersedia', $pinjam->jumlah);
            return back()->with('error', 'Stok tidak mencukupi untuk perubahan ini!');
        }

        $pinjam->update([
            'user_id' => $request->user_id,
            'jumlah' => $request->jumlah,
            'tgl_kembali' => now()->addDays($request->durasi),
        ]);

        $alat->decrement('stok_tersedia', $request->jumlah);

        return redirect()->back()->with('success', 'Data peminjaman berhasil diperbarui!');
    }

    public function destroyPeminjaman($id)
    {
        $pinjam = Peminjaman::findOrFail($id);

        if ($pinjam->status == 'disetujui' || $pinjam->status == 'menunggu') {
            $pinjam->alat->increment('stok_tersedia', $pinjam->jumlah);
        }

        $pinjam->delete();
        return redirect()->back()->with('success', 'Data peminjaman telah dihapus.');
    }

    public function verifikasiPeminjaman(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);
        $status = $request->status;

        if ($status == 'ditolak') {
            $pinjam->alat->increment('stok_tersedia', $pinjam->jumlah);
        }

        $pinjam->update(['status' => $status]);

        return redirect()->back()->with('success', 'Status peminjaman diperbarui menjadi ' . $status);
    }

    public function kembalikanPeminjaman(Request $request, $id)
    {
        // Gunakan findOrFail agar tidak error kalau ID tidak ada
        $pinjam = Peminjaman::with('alat')->findOrFail($id);
        $kondisi = $request->kondisi ?? 'baik'; 
        $waktuSekarang = \Carbon\Carbon::now('Asia/Jakarta');
        $total_denda = 0;

        // --- PERBAIKAN LOGIKA DENDA TERLAMBAT ---
        // Gunakan endOfDay agar user punya waktu sampai jam 23:59 untuk mengembalikan
        $deadline = \Carbon\Carbon::parse($pinjam->tgl_kembali)->endOfDay();

        if ($waktuSekarang->gt($deadline)) {
            // Hitung selisih hari dari tgl_kembali (awal hari) ke sekarang
            $selisihHari = $waktuSekarang->diffInDays(\Carbon\Carbon::parse($pinjam->tgl_kembali)->startOfDay());
            $total_denda = $selisihHari * 5000;
        }

        // --- LOGIKA DENDA KONDISI ---
        if ($kondisi == 'hilang') {
            // Pastikan harga_asli ada di database
            $hargaAlat = $pinjam->alat->harga_asli ?? $pinjam->alat->harga ?? 0;
            $total_denda += ($hargaAlat * $pinjam->jumlah);
        } elseif ($kondisi == 'rusak') {
            $total_denda += 20000;
        }

        // --- UPDATE DATA ---
        $pinjam->update([
            'status' => 'selesai',
            'kondisi' => $kondisi,
            'total_denda' => $total_denda,
            'tgl_dikembalikan' => $waktuSekarang,
        ]);

        // Update stok jika barang tidak hilang
        if ($kondisi != 'hilang') {
            // Gunakan relasi method () agar lebih stabil
            $pinjam->alat()->increment('stok_tersedia', $pinjam->jumlah);
        }

        return redirect()->back()->with('success', 'Berhasil dikembalikan! Total Denda: Rp ' . number_format($total_denda, 0, ',', '.'));
    }

    /* ======================
    CRUD PENGEMBALIAN
    ====================== */
    public function pengembalian(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat'])
                ->where('status', 'selesai');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('alat', function($qAlat) use ($search) {
                    $qAlat->where('nama_alat', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($qUser) use ($search) {
                    $qUser->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Sortir berdasarkan tanggal dikembalikan terbaru
        $peminjamans = $query->latest('tgl_dikembalikan')->paginate(10);
            
        return view('admin.pengembalian', compact('peminjamans'));
        }
}
