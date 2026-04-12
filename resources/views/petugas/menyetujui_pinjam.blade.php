@extends('layouts.petugas')

@section('title', 'Menyetujui Peminjaman')

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
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic uppercase">Persetujuan <span class="text-emerald-600">Peminjaman</span></h1>
        <p class="text-gray-500 font-medium uppercase text-[10px] tracking-widest mt-1">Validasi permintaan alat dari atlet</p>
    </div>
    <div class="flex gap-3">
        <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">{{ $peminjamans->count() }} Permintaan Menunggu</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                    <th class="pb-5 px-4 w-32">Kode Pinjam</th>
                    <th class="pb-5 px-4">Data Atlet</th>
                    <th class="pb-5 px-4 text-center">Alat Olahraga</th>
                    <th class="pb-5 px-4 text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($peminjamans as $index => $item)
                    <tr class="group hover:bg-gray-50/50 transition-all text-sm">
                        <td class="py-6 px-4">
                            <span class="font-bold text-gray-900 text-sm bg-gray-100 px-2 py-1 rounded-lg">
                                PJM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        <td class="py-6 px-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-[#062c21] rounded-xl flex items-center justify-center text-emerald-400 font-black shadow-lg">
                                    {{ substr($item->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase italic">{{ $item->user->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                        {{ $item->user->email }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="py-6 px-4 text-center">
                            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">
                                {{ $item->alat->nama_alat }} <span class="text-emerald-600">({{ $item->jumlah }}x)</span>
                            </span>
                        </td>

                        <td class="py-6 px-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="showDetail({{ $item->id }})" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-black px-3 py-3 rounded-xl transition-all shadow-md active:scale-95 uppercase tracking-widest"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <form action="{{ route('petugas.pinjam.proses', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="disetujui">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black px-4 py-3 rounded-xl transition-all shadow-md active:scale-95 uppercase tracking-widest">
                                        Setuju
                                    </button>
                                </form>

                                <form action="{{ route('petugas.pinjam.proses', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak permintaan ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white text-[10px] font-black px-4 py-3 rounded-xl transition-all uppercase tracking-widest">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <tr id="detail-{{ $item->id }}" class="hidden bg-gray-50/80">
                        <td colspan="4" class="px-6 py-4">
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                            <i class="fas fa-info-circle mr-1"></i> Catatan / Tujuan Peminjaman
                                        </p>
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <p class="text-sm text-gray-700 italic">
                                                {{ $item->tujuan ?? 'Tidak ada catatan' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> Detail Waktu
                                        </p>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Tanggal Pinjam:</span>
                                                <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Batas Kembali:</span>
                                                <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Durasi:</span>
                                                <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->diffInDays($item->tgl_kembali) }} Hari</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Tgl Pengajuan:</span>
                                                <span class="font-semibold text-gray-800">{{ $item->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-gray-500">Jumlah:</span>
                                                <span class="font-semibold text-gray-800">{{ $item->jumlah }} Unit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                    <button onclick="hideDetail({{ $item->id }})" 
                                            class="text-[10px] font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-times-circle mr-1"></i> Tutup Detail
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-32 text-center px-4">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-clipboard-list text-emerald-500 text-3xl"></i>
                                </div>
                                <p class="text-gray-400 font-black uppercase italic tracking-[0.3em] text-[10px]">Tidak ada permintaan peminjaman saat ini.</p>
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
            <span>Validasi data atlet sebelum menyetujui</span>
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
            detailRow.classList.remove('hidden');
            detailRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
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