@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')

@section('content')
<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase italic">Riwayat <span class="text-emerald-600">Pengembalian</span></h1>
        <p class="text-gray-500 font-medium text-sm">Log pengembalian alat berdasarkan kondisi dan denda.</p>
    </div>
    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-2 rounded-2xl text-xs font-bold animate-bounce">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif
</div>

<form action="{{ route('admin.pengembalian') }}" method="GET" class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 mb-6">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam atau alat..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold">
    </div>
    <button type="submit" class="bg-[#062c21] text-white px-8 py-3 rounded-2xl font-bold text-sm hover:bg-emerald-900 transition-all">
        Cari Data
    </button>
</form>

<div class="bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-36">Kode Pinjam</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alat & Peminjam</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tgl Dikembalikan</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kondisi</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Total Denda</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($peminjamans as $data)
                <tr class="hover:bg-emerald-50/30 transition-colors group">
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-gray-900 text-sm bg-gray-100 px-2 py-1 rounded-lg">
                            PJM-{{ str_pad($data->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 shrink-0 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold uppercase">
                                {{ substr($data->alat->nama_alat ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm mb-0.5 leading-none">{{ $data->alat->nama_alat ?? 'Alat Dihapus' }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $data->user->name ?? 'User Unknown' }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-emerald-600">
                                {{ $data->tgl_dikembalikan ? \Carbon\Carbon::parse($data->tgl_dikembalikan)->translatedFormat('d M Y') : '-' }}
                            </span>
                            <span class="text-[10px] text-gray-400 italic font-medium">
                                Batas: {{ \Carbon\Carbon::parse($data->tgl_kembali)->format('d/m/Y') }}
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php 
                            $knd = strtolower($data->kondisi ?? 'baik'); 
                            
                            $warna = match($knd) {
                                'baik' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'lecet' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'rusak' => 'bg-red-100 text-red-700 border-red-200',
                                'hilang' => 'bg-black text-white border-gray-800',
                                default => 'bg-gray-100 text-gray-700 border-gray-200'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $warna }}">
                            {{ strtoupper($data->kondisi ?? 'BAIK') }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php
                            $nilaiDenda = (float) ($data->total_denda ?? 0);
                        @endphp

                        @if($nilaiDenda > 0)
                            <div class="flex flex-col">
                                <span class="text-rose-600 font-black text-sm">
                                    Rp {{ number_format($nilaiDenda, 0, ',', '.') }}
                                </span>
                                <span class="text-[8px] text-rose-400 font-bold uppercase italic tracking-tighter">
                                    Denda Terbayar
                                </span>
                            </div>
                        @else
                            <div class="flex flex-col items-center">
                                <span class="text-emerald-500 font-bold text-[10px] uppercase">Lunas</span>
                                <span class="text-[8px] text-emerald-300 font-medium italic">Tanpa Denda</span>
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.peminjaman.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus permanen riwayat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition-all border border-gray-200">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-20 text-center text-gray-400 font-bold italic">
                        <i class="fas fa-box-open block text-4xl mb-3 opacity-20"></i>
                        Tidak ada data pengembalian ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="p-6 bg-gray-50/50 flex items-center justify-between border-t border-gray-100">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            Showing {{ $peminjamans->firstItem() ?? 0 }} to {{ $peminjamans->lastItem() ?? 0 }} of {{ $peminjamans->total() }} results
        </p>
        <div class="pagination-custom">
            {{ $peminjamans->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection