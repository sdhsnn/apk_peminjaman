<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasController extends Controller
{
    public function index() 
    {
        // Menunggu approval (status pending)
        $waitingApproval = Peminjaman::where('status', 'pending')->count();
        
        // Alat yang sedang dipinjam (status disetujui)
        $alatDipinjam = Peminjaman::where('status', 'disetujui')->sum('jumlah');
        
        // Selesai hari ini (status selesai atau dikembalikan)
        $selesaiHariIni = Peminjaman::where('status', 'selesai')
                                    ->whereDate('tgl_dikembalikan', Carbon::today())
                                    ->count();

        // Antrean tugas (pending dan dikembalikan)
        $antreanTugas = Peminjaman::with(['user', 'alat'])
                                    ->whereIn('status', ['pending', 'dikembalikan'])
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();

        return view('petugas.dashboard', compact(
            'waitingApproval', 
            'alatDipinjam', 
            'selesaiHariIni', 
            'antreanTugas'
        ));
    }

    /* ============================
       PROSES MENYETUJUI PEMINJAMAN
       ============================ */
    public function menyetujuiPeminjaman() 
    {
        $peminjamans = Peminjaman::with(['user', 'alat'])
                    ->where('status', 'pending')
                    ->latest()
                    ->get();

        return view('petugas.menyetujui_pinjam', compact('peminjamans'));
    }

    public function prosesPersetujuanPinjam(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $alat = $peminjaman->alat;

        if ($request->status == 'disetujui') {
            if ($alat->stok_tersedia < $peminjaman->jumlah) {
                return back()->with('error', 'Stok alat tidak mencukupi!');
            }
            
            $alat->decrement('stok_tersedia', $peminjaman->jumlah);
            $peminjaman->update(['status' => 'disetujui']);
            
        } elseif ($request->status == 'ditolak') {
            $peminjaman->update(['status' => 'ditolak']);
            
        } elseif ($request->has('kondisi')) {
            $peminjaman->update([
                'status' => 'dikembalikan',
                'catatan' => $request->kondisi
            ]);
            
            $alat->increment('stok_tersedia', $peminjaman->jumlah);
            return redirect()->back()->with('success', 'Alat berhasil dikembalikan!');
        }

        return back()->with('success', 'Status berhasil diperbarui!');
    }

    /* ==============================
       PROSES MENYETUJUI PENGEMBALIAN
       ============================== */
    public function menyetujuiPengembalian() 
    {
        $pengembalians = Peminjaman::with(['user', 'alat'])
            ->where('status', 'dikembalikan')
            ->latest()
            ->get();

        return view('petugas.menyetujui_kembali', compact('pengembalians'));
    }

    public function prosesKonfirmasiKembali(Request $request, $id)
    {
        $request->validate([
            'kondisi' => 'required|in:baik,lecet,rusak,hilang',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pinjam = Peminjaman::with('alat')->findOrFail($id);
        
        if ($pinjam->status != 'dikembalikan') {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk dikonfirmasi!');
        }
        
        $kondisiBaru = $request->kondisi;
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $total_denda = 0;
        
        // HITUNG DENDA KETERLAMBATAN
        $deadline = Carbon::parse($pinjam->tgl_kembali)->endOfDay();
        
        if ($waktuSekarang->gt($deadline)) {
            $selisihHari = $waktuSekarang->diffInDays(Carbon::parse($pinjam->tgl_kembali)->startOfDay());
            $total_denda += ($selisihHari * 5000);
        }
        
        // HITUNG DENDA KONDISI
        switch ($kondisiBaru) {
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
        
        // ========== LOGIKA STOK ==========
        $alat = $pinjam->alat;
        
        if ($kondisiBaru == 'hilang' || $kondisiBaru == 'rusak') {
            // HILANG atau RUSAK: stok tetap berkurang (tidak dikembalikan)
            $alat->decrement('stok_tersedia', $pinjam->jumlah);
            $alat->decrement('stok_total', $pinjam->jumlah);
            
        } elseif ($kondisiBaru == 'lecet') {
            // LECET: stok dikembalikan (masih bisa dipinjam)
            $alat->increment('stok_tersedia', $pinjam->jumlah);
            // Stok total tetap (tidak berkurang)
            
        } elseif ($kondisiBaru == 'baik') {
            // BAIK: stok dikembalikan normal
            $alat->increment('stok_tersedia', $pinjam->jumlah);
            // Stok total tetap
        }
        
        // UPDATE DATA PEMINJAMAN
        $pinjam->update([
            'status'           => 'selesai',
            'kondisi'          => $kondisiBaru,
            'total_denda'      => $total_denda,
            'tgl_dikembalikan' => $waktuSekarang,
            'tujuan'           => $request->catatan ?? $pinjam->tujuan,
        ]);
        
        $message = "Pengembalian berhasil diproses! Kondisi: " . ucfirst($kondisiBaru) . 
                " | Total Denda: Rp " . number_format($total_denda, 0, ',', '.');
        
        return redirect()->route('petugas.menyetujui_kembali')->with('success', $message);
    }

    public function cetakLaporan() 
    {
        $laporans = \App\Models\Peminjaman::with(['user', 'alat'])->latest()->get();

        return view('petugas.laporan', compact('laporans'));
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $query = Peminjaman::with(['user', 'alat']);

        if ($tgl_mulai && $tgl_selesai) {
            // PERBAIKAN: Gunakan whereDate agar menangkap seluruh tanggal
            $query->whereDate('created_at', '>=', $tgl_mulai)
                ->whereDate('created_at', '<=', $tgl_selesai);
        }

        $laporans = $query->latest()->get();

        $pdf = Pdf::loadView('petugas.laporan_pdf', compact('laporans', 'tgl_mulai', 'tgl_selesai'));
        
        return $pdf->download('laporan-peminjaman-' . date('Y-m-d') . '.pdf');
    }
}
