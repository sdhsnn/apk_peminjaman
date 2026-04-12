@extends('layouts.petugas')

@section('title', 'Menyetujui Kembali')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center gap-3 animate-pulse">
    <i class="fas fa-check-circle text-xl"></i>
    <span class="font-bold text-sm uppercase tracking-wider">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-rose-500 text-white rounded-2xl shadow-lg shadow-rose-500/20 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <span class="font-bold text-sm uppercase tracking-wider">{{ session('error') }}</span>
</div>
@endif

<div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic uppercase">Persetujuan <span class="text-emerald-600">Kembali</span></h1>
        <p class="text-gray-500 font-medium uppercase text-[10px] tracking-widest mt-1">Verifikasi kondisi fisik & hitung denda otomatis</p>
    </div>
    <div class="flex gap-3">
        <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">{{ $pengembalians->count() }} Alat Perlu Dicek</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                    <th class="pb-5 px-4 w-32">Kode Pinjam</th>
                    <th class="pb-5 px-4">Alat Olahraga</th>
                    <th class="pb-5 px-4">Peminjam</th> 
                    <th class="pb-5 px-4 text-center">Pilih Kondisi Fisik</th>
                    <th class="pb-5 px-4 text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengembalians as $data)
                    <tr class="group hover:bg-gray-50/50 transition-all text-sm">
                        <td class="py-6 px-4">
                            <span class="font-bold text-gray-900 text-sm bg-gray-100 px-2 py-1 rounded-lg">
                                PJM-{{ str_pad($data->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        <td class="py-6 px-4">
                            <div>
                                <p class="font-bold text-gray-900 leading-none mb-1 text-sm">{{ $data->alat->nama_alat }}</p>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Qty: {{ $data->jumlah }} Unit
                                </span>
                            </div>
                        </td>

                        <td class="py-6 px-4">
                            <span class="font-black text-gray-900 uppercase tracking-tight block leading-none mb-1">
                                {{ $data->user->name }}
                            </span>
                            <span class="text-[9px] text-gray-400 font-bold italic uppercase tracking-wider">
                                Batas: {{ \Carbon\Carbon::parse($data->tgl_kembali)->format('d/m/Y') }}
                            </span>
                        </td>
                        
                        <form action="{{ route('petugas.kembali.proses', $data->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <td class="py-6 px-4">
                                <div class="flex justify-center gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="kondisi" value="baik" class="hidden peer" required>
                                        <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                            Baik
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="kondisi" value="lecet" class="hidden peer">
                                        <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-yellow-400 peer-checked:text-white peer-checked:border-yellow-400 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                            Lecet
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" name="kondisi" value="rusak" class="hidden peer">
                                        <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                            Rusak
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" name="kondisi" value="hilang" class="hidden peer">
                                        <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                            Hilang
                                        </div>
                                    </label>
                                </div>
                            </td>

                            <td class="py-6 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="showDetail({{ $data->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-black px-3 py-3 rounded-xl transition-all shadow-md active:scale-95 uppercase tracking-widest"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="submit" class="bg-[#062c21] hover:bg-emerald-800 text-white text-[10px] font-black px-6 py-3 rounded-2xl transition-all shadow-lg shadow-emerald-900/10 uppercase tracking-widest active:scale-95">
                                        Konfirmasi
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>

                    <!-- ROW DETAIL (Hidden) -->
                    <tr id="detail-{{ $data->id }}" class="hidden bg-gray-50/80">
                        <td colspan="5" class="px-6 py-4">
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                            <i class="fas fa-info-circle mr-1"></i> Catatan / Tujuan Peminjaman
                                        </p>
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <p class="text-sm text-gray-700 italic">
                                                {{ $data->tujuan ?? 'Tidak ada catatan' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Estimasi Denda
                                        </p>
                                        <div class="space-y-2">
                                            @php
                                                $estimasiTerlambat = 0;
                                                $deadline = \Carbon\Carbon::parse($data->tgl_kembali)->startOfDay();
                                                $sekarang = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
                                                if ($sekarang->gt($deadline)) {
                                                    $selisihHari = $deadline->diffInDays($sekarang);
                                                    $estimasiTerlambat = $selisihHari * 5000;
                                                }
                                            @endphp
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Denda Terlambat:</span>
                                                <span class="font-semibold {{ $estimasiTerlambat > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                    Rp {{ number_format($estimasiTerlambat, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Denda Baik:</span>
                                                <span class="font-semibold text-gray-800">Rp 0</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Denda Lecet:</span>
                                                <span class="font-semibold text-yellow-600">Rp 15.000</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Denda Rusak:</span>
                                                <span class="font-semibold text-red-600">Rp 50.000</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Denda Hilang:</span>
                                                <span class="font-semibold text-black">Harga Alat × Jumlah</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                    <button onclick="hideDetail({{ $data->id }})" 
                                            class="text-[10px] font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-times-circle mr-1"></i> Tutup Detail
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-32 text-center px-4">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-check-double text-emerald-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-400 font-black uppercase italic tracking-[0.3em] text-[10px]">Gudang Clear! Tidak ada antrian cek alat.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-12 pt-8 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-4 text-[10px] font-black text-gray-400 uppercase italic tracking-widest">
        <div class="flex items-center gap-2">
            <i class="fas fa-shield-alt text-emerald-500"></i>
            <span>Verifikasi kondisi fisik menentukan denda akhir</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fas fa-clock text-gray-300"></i>
            <span>Sistem mencatat waktu real-time</span>
        </div>
    </div>
</div>

<script>
    function showDetail(id) {
        const detailRow = document.getElementById('detail-' + id);
        if (detailRow) {
            detailRow.classList.toggle('hidden');
            if (!detailRow.classList.contains('hidden')) {
                detailRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    function hideDetail(id) {
        const detailRow = document.getElementById('detail-' + id);
        if (detailRow) {
            detailRow.classList.add('hidden');
        }
    }
</script>
@endsection