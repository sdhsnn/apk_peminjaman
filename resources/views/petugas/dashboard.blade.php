@extends('layouts.petugas')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic">Dashboard Petugas</h1>
        <p class="text-gray-500 font-medium italic">Pantau aktivitas peminjaman hari ini secara real-time.</p>
    </div>
    <div class="flex gap-3">
        <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg text-xs">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-500 italic font-black text-xl">
            <i class="fas fa-hourglass-start"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Menunggu Approval</p>
            <h3 class="text-2xl font-black text-gray-900">{{ str_pad($waitingApproval, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Permintaan</span></h3>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="bg-orange-50 p-4 rounded-2xl text-orange-500 italic font-black text-xl">
            <i class="fas fa-box-open"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Alat Dipinjam</p>
            <h3 class="text-2xl font-black text-gray-900">{{ str_pad($alatDipinjam, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Unit</span></h3>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="bg-blue-50 p-4 rounded-2xl text-blue-500 italic font-black text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Selesai Hari Ini</p>
            <h3 class="text-2xl font-black text-gray-900">{{ str_pad($selesaiHariIni, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Transaksi</span></h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-xl font-bold text-gray-900 italic uppercase tracking-tighter">Antrean Tugas Terbaru</h2>
        <span class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full uppercase italic">Segera Proses</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                    <th class="pb-4 px-2">Atlet</th>
                    <th class="pb-4">Alat Olahraga</th>
                    <th class="pb-4">Kode</th>
                    <th class="pb-4">Jenis Tugas</th>
                    <th class="pb-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($antreanTugas as $tugas)
                <tr class="group hover:bg-gray-50/50 transition-all">
                    <td class="py-5 px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#062c21] rounded-xl flex items-center justify-center text-emerald-400 font-bold shadow-sm flex-shrink-0">
                                {{ strtoupper(substr($tugas->user->name, 0, 2)) }}
                            </div>
                            <span class="font-bold text-sm text-gray-900 uppercase italic">{{ $tugas->user->name }}</span>
                        </div>
                    </td>
                    <td class="py-5">
                        <span class="text-sm font-semibold text-gray-600 block">{{ $tugas->alat->nama_alat }}</span>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded italic">
                            Qty: {{ $tugas->jumlah }}
                        </span>
                    </td>
                    <td class="py-5">
                        <span class="font-bold text-gray-900 text-sm bg-gray-100 px-2 py-1 rounded-lg">
                            PJM-{{ str_pad($tugas->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td class="py-5">
                        @if($tugas->status == 'pending')
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase italic border border-emerald-200">
                                <i class="fas fa-arrow-up mr-1"></i> Peminjaman
                            </span>
                        @elseif($tugas->status == 'dikembalikan')
                            <span class="bg-purple-100 text-purple-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase italic border border-purple-200">
                                <i class="fas fa-arrow-down mr-1"></i> Pengembalian
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-[10px] font-black px-3 py-1 rounded-lg uppercase italic border border-gray-200">
                                <i class="fas fa-clock mr-1"></i> {{ $tugas->status }}
                            </span>
                        @endif
                    </td>
                    <td class="py-5 text-center">
                        @if($tugas->status == 'pending')
                            <a href="{{ route('petugas.menyetujui_peminjaman') }}" 
                            class="bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black px-6 py-2.5 rounded-xl transition-all shadow-md uppercase tracking-widest inline-block active:scale-95">
                                Setujui
                            </a>
                        @elseif($tugas->status == 'dikembalikan')
                            <a href="{{ route('petugas.menyetujui_kembali') }}" 
                            class="bg-gray-900 hover:bg-emerald-800 text-white text-[10px] font-black px-6 py-2.5 rounded-xl transition-all shadow-md uppercase tracking-widest inline-block active:scale-95">
                                Cek Alat
                            </a>
                        @else
                            <span class="text-gray-400 text-[9px] font-bold uppercase">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-300">
                            <i class="fas fa-tasks text-4xl mb-4"></i>
                            <p class="text-xs font-black uppercase tracking-[0.2em] italic">Antrean Bersih / Kosong</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection