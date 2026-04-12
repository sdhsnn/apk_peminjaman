<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /* ======================
       CRUD PEMINJAMAN
       ====================== */
    public function peminjaman()
    {
        $users = User::where('role', 'peminjam')->orderBy('name')->get();
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        
        // TAMPILKAN SEMUA STATUS (pending, disetujui, selesai, ditolak, dikembalikan)
        $peminjamanBerlangsung = Peminjaman::with(['user', 'alat'])
            ->latest()
            ->paginate(10);

        return view('admin.peminjaman', compact('users', 'alats', 'peminjamanBerlangsung'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'alat_id' => 'required|exists:alats,id',
            'jumlah'  => 'required|integer|min:1',
            'tgl_kembali' => 'required|date|after_or_equal:today',
            'tujuan'  => 'required|string|max:500',
        ]);

        $tglPinjam = now()->startOfDay(); 
        $tglKembali = Carbon::parse($request->tgl_kembali)->startOfDay();
        
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > 3) {
            return back()->with('error', "Batas maksimal peminjaman adalah 3 hari!");
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
            'tujuan'      => $request->tujuan,
            'status'      => 'pending',
        ]);
        
        return redirect()->back()->with('success', 'Permintaan peminjaman terkirim!');
    }

    public function destroyPeminjaman($id)
    {
        $pinjam = Peminjaman::findOrFail($id);

        // Jika status disetujui, kembalikan stok dulu
        if ($pinjam->status == 'disetujui') {
            $pinjam->alat->increment('stok_tersedia', $pinjam->jumlah);
        }

        $pinjam->delete();
        return redirect()->back()->with('success', 'Data peminjaman telah dihapus.');
    }

    public function verifikasiPeminjaman(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);
        $status = $request->status;

        if ($status == 'disetujui') {
            // CEK STOK
            if ($pinjam->alat->stok_tersedia < $pinjam->jumlah) {
                return back()->with('error', 'Stok alat tidak mencukupi! Stok tersedia: ' . $pinjam->alat->stok_tersedia);
            }
            
            // KURANGI STOK
            $pinjam->alat->decrement('stok_tersedia', $pinjam->jumlah);
            $pinjam->update(['status' => 'disetujui']);
            
            return back()->with('success', 'Peminjaman berhasil DISETUJUI!');
            
        } elseif ($status == 'ditolak') {
            $pinjam->update(['status' => 'ditolak']);
            return back()->with('success', 'Peminjaman DITOLAK!');
        }

        return back()->with('error', 'Status tidak valid!');
    }

    /**
     * ADMIN LANGSUNG KEMBALIKAN ALAT (dengan pilih kondisi)
     * HANYA UNTUK STATUS 'disetujui'
     */
    public function kembalikanPeminjaman(Request $request, $id)
    {
        $request->validate([
            'kondisi' => 'required|in:baik,lecet,rusak,hilang',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pinjam = Peminjaman::with(['alat', 'user'])->findOrFail($id);
        
        if ($pinjam->status != 'disetujui') {
            return redirect()->back()->with('error', 'Status peminjaman harus "disetujui" untuk diproses pengembalian!');
        }
        
        $kondisi = $request->kondisi;
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $total_denda = 0;
        
        // Denda keterlambatan
        $deadline = Carbon::parse($pinjam->tgl_kembali)->startOfDay();
        $tanggalKembali = $waktuSekarang->copy()->startOfDay();
        
        if ($tanggalKembali->gt($deadline)) {
            $selisihHari = $deadline->diffInDays($tanggalKembali);
            $total_denda += ($selisihHari * 5000);
        }
        
        // Denda kondisi
        switch ($kondisi) {
            case 'hilang':
                $hargaAlat = $pinjam->alat->harga_asli ?? $pinjam->alat->harga_sewa ?? 0;
                $total_denda += ($hargaAlat * $pinjam->jumlah);
                break;
            case 'rusak':
                $total_denda += 50000;
                break;
            case 'lecet':
                $total_denda += 15000;
                break;
            case 'baik':
                $total_denda += 0;
                break;
        }
        
        $total_denda = max(0, $total_denda);
        
        $updateData = [
            'status'           => 'selesai',
            'kondisi'          => $kondisi,
            'total_denda'      => $total_denda,
            'tgl_dikembalikan' => $waktuSekarang,
        ];
        
        if ($request->filled('catatan')) {
            $updateData['tujuan'] = $request->catatan;
        }
        
        $pinjam->update($updateData);
        
        // ========== LOGIKA STOK YANG BENAR ==========
        if ($kondisi == 'baik' || $kondisi == 'lecet') {
            // BAIK atau LECET: stok dikembalikan (masih bisa dipinjam)
            $pinjam->alat()->increment('stok_tersedia', $pinjam->jumlah);
        }
        // RUSAK atau HILANG: stok TIDAK dikembalikan (tidak bisa dipinjam)
        
        $message = "Pengembalian berhasil diproses! Kondisi: " . ucfirst($kondisi) . 
                " | Total Denda: Rp " . number_format($total_denda, 0, ',', '.');
        
        return redirect()->route('admin.pengembalian')->with('success', $message);
    }

    /* ======================
    CRUD PENGEMBALIAN (RIWAYAT)
    ====================== */
    public function pengembalian(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat'])
                ->where('status', 'selesai')
                ->whereNotNull('tgl_dikembalikan');

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

        $peminjamans = $query->latest('tgl_dikembalikan')->paginate(10);
            
        return view('admin.pengembalian', compact('peminjamans'));
    }
}