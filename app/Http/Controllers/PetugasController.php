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
        $waitingApproval = Peminjaman::where('status', 'pending')->count();
        $alatDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $selesaiHariIni = Peminjaman::where('status', 'dikembalikan')
                                    ->whereDate('updated_at', \Carbon\Carbon::today())
                                    ->count();

        $antreanTugas = Peminjaman::with(['user', 'alat'])
                                    ->where('status', 'pending')
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
        $pinjam = Peminjaman::with('alat')->findOrFail($id);
        
        // Ambil data dari request
        $kondisi = $request->input('kondisi', 'baik');
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        
        // Inisialisasi Denda
        $total_denda = 0;

        // --- LOGIKA 1: DENDA KETERLAMBATAN ---
        $deadline = Carbon::parse($pinjam->tgl_kembali)->startOfDay();
        $hariIni = $waktuSekarang->copy()->startOfDay();

        if ($hariIni->gt($deadline)) {
            $selisihHari = $hariIni->diffInDays($deadline);
            $total_denda += ($selisihHari * 5000); // Misal: Rp 5.000 per hari
        }

        // --- LOGIKA 2: DENDA KONDISI ---
        if ($kondisi == 'rusak') {
            $total_denda += 20000;
        } elseif ($kondisi == 'lecet') {
            $total_denda += 5000;
        } elseif ($kondisi == 'hilang') {
            // Jika hilang, denda seharga alat (asumsi ada kolom harga di table alat)
            $hargaAlat = $pinjam->alat->harga ?? 100000; 
            $total_denda += ($hargaAlat * $pinjam->jumlah);
        }

        // --- SIMPAN DATA ---
        $pinjam->update([
            'status'           => 'selesai',
            'kondisi'          => $kondisi,
            'tgl_dikembalikan' => $waktuSekarang,
            'total_denda'      => $total_denda,
            'catatan'          => $request->catatan
        ]);

        // Kembalikan Stok Alat
        if ($kondisi != 'hilang') {
            $pinjam->alat->increment('stok', $pinjam->jumlah);
        }

        return redirect()->route('admin.pengembalian')->with('success', 'Pengembalian berhasil diproses!');
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
            $query->whereBetween('created_at', [$tgl_mulai, $tgl_selesai]);
        }

        $laporans = $query->latest()->get();

        $pdf = Pdf::loadView('petugas.laporan_pdf', compact('laporans', 'tgl_mulai', 'tgl_selesai'));
        
        return $pdf->download('laporan-peminjaman.pdf');
    }
}
