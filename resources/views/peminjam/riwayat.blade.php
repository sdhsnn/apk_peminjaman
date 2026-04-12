@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-900 italic uppercase tracking-tighter">Riwayat <span class="text-emerald-600">Peminjaman</span></h1>
        <p class="text-slate-500 text-[10px] font-bold mt-1 uppercase tracking-widest italic">Semua aktivitas peminjaman alat Anda</p>
    </div>

    <div class="bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 card-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-24">Kode</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Alat</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Jumlah</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $item)
                        @php
                            $statusColor = match($item->status) {
                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'disetujui' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'ditolak' => 'bg-rose-100 text-rose-700 border-rose-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-slate-900 text-sm bg-slate-100 px-2 py-1 rounded-lg">
                                    PJM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                        @if($item->alat->foto)
                                            <img src="{{ asset('storage/' . $item->alat->foto) }}" class="w-full h-full object-cover rounded-xl">
                                        @else
                                            <i class="fas fa-running text-emerald-500 text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $item->alat->nama_alat }}</p>
                                        <p class="text-[9px] text-slate-400 uppercase">{{ $item->alat->kategori }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $item->jumlah }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $statusColor }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="{{ $item->total_denda > 0 ? 'text-rose-600' : 'text-slate-400' }} font-bold text-sm">
                                    Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-slate-400 font-bold uppercase text-xs">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50/50 border-t border-slate-100">
            {{ $riwayat->links() }}
        </div>
    </div>
@endsection