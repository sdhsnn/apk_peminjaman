<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;

class AlatController extends Controller
{
    public function alat(Request $request)
    {
        $query = Alat::query();

        if ($request->filled('search')) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori') && $request->kategori != 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        $alats = $query->latest()->get();
        return view('admin.alat', compact('alats'));
    }

    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required',
            'kategori' => 'required',
            'stok_total' => 'required|numeric',
            'harga_sewa' => 'required|numeric',
            'harga_asli' => 'required|numeric', // Tambahkan ini
            'kondisi' => 'required',
            'foto' => 'nullable|image|max:2048'
        ]);

        $path = $request->hasFile('foto') ? $request->file('foto')->store('alats', 'public') : null;

        Alat::create([
            'nama_alat' => $request->nama_alat,
            'slug' => \Illuminate\Support\Str::slug($request->nama_alat) . '-' . \Illuminate\Support\Str::random(5),
            'kategori' => $request->kategori,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_total,
            'harga_sewa' => $request->harga_sewa,
            'harga_asli' => $request->harga_asli, // Simpan harga asli
            'kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
            'foto' => $path,
        ]);

        return back()->with('success', 'Alat berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);
        
        $request->validate([
            'nama_alat' => 'required',
            'stok_total' => 'required|numeric',
            'harga_sewa' => 'required|numeric',
            'harga_asli' => 'required|numeric', // Tambahkan ini
            'kondisi' => 'required'
        ]);

        if ($request->hasFile('foto')) {
            if ($alat->foto) \Storage::disk('public')->delete($alat->foto);
            $alat->foto = $request->file('foto')->store('alats', 'public');
        }

        $alat->update([
            'nama_alat' => $request->nama_alat,
            'kategori' => $request->kategori,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_total, // Opsional: logika stok bisa disesuaikan
            'harga_sewa' => $request->harga_sewa,
            'harga_asli' => $request->harga_asli, // Update harga asli
            'kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Data diperbarui!');
    }

    public function destroy($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->foto) {
            Storage::disk('public')->delete($alat->foto);
        }

        $alat->delete();

        return redirect()->back()->with('success', 'Peralatan berhasil dihapus dari katalog.');
    }
}
