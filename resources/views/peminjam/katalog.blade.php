@extends('layouts.peminjam')

@section('title', 'Katalog Alat')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic uppercase">Mau Latihan <span class="text-emerald-600">Apa Hari Ini?</span></h1>
            <p class="text-gray-500 text-sm font-medium">Pilih alat olahraga kualitas terbaik untuk performamu.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('peminjam.katalog') }}" method="GET" class="relative group">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari raket, bola..." 
                    class="w-64 pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all card-shadow font-semibold">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
            </form>
        </div>
    </div>

    {{-- KATEGORI TABS --}}
    <div class="flex items-center gap-3 mb-8 overflow-x-auto pb-2 no-scrollbar">
        @php 
            $categories = ['Semua' => 'fa-th-large', 'Bola' => 'fa-volleyball-ball', 'Raket' => 'fa-table-tennis', 'Fitness' => 'fa-dumbbell'];
        @endphp
        @foreach($categories as $cat => $icon)
            <a href="{{ route('peminjam.katalog', ['kategori' => $cat, 'search' => request('search')]) }}" 
               class="px-6 py-2.5 rounded-full text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2
               {{ (request('kategori', 'Semua') == $cat) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                <i class="fas {{ $icon }}"></i> {{ $cat }}
            </a>
        @endforeach
    </div>

    {{-- GRID ALAT --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($alats as $alat)
        <div class="group bg-white rounded-[2.5rem] p-5 border border-gray-100 card-shadow hover:border-emerald-300 transition-all duration-300">
            <div class="relative h-64 w-full bg-gray-50 rounded-[2rem] overflow-hidden mb-5">
                <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-tighter shadow-sm z-10
                    {{ $alat->kondisi == 'baik' ? 'text-emerald-600' : ($alat->kondisi == 'lecet' ? 'text-amber-500' : 'text-rose-600') }}">
                    Kondisi: {{ $alat->kondisi }}
                </span>

                <div class="w-full h-full flex items-center justify-center">
                    @if($alat->foto)
                        <img src="{{ asset('storage/' . $alat->foto) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <i class="fas fa-running fa-4x text-gray-200 group-hover:text-emerald-200 transition-colors"></i>
                    @endif
                </div>

                <span class="absolute bottom-4 right-4 {{ $alat->stok_tersedia > 0 ? 'bg-emerald-500' : 'bg-gray-800' }} text-white text-[9px] font-black px-3 py-1 rounded-lg shadow-lg">
                    {{ $alat->stok_tersedia > 0 ? 'TERSEDIA' : 'KOSONG' }}
                </span>
            </div>

            <div class="px-2 pb-2">
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">{{ $alat->kategori }}</p>
                <h3 class="text-xl font-extrabold text-gray-900 leading-tight mb-2 truncate uppercase italic tracking-tighter">{{ $alat->nama_alat }}</h3>
                
                <p class="text-emerald-600 font-black text-base mb-4">
                    Rp {{ number_format($alat->harga_sewa, 0, ',', '.') }}<span class="text-[10px] text-gray-400 font-normal"> / hari</span>
                </p>

                <div class="flex items-center justify-between py-3 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Stok Unit</p>
                        <p class="text-lg font-black text-gray-900">
                            {{ $alat->stok_tersedia }} <span class="text-xs font-medium text-gray-300">Unit</span>
                        </p>
                    </div>
                    
                    @if($alat->stok_tersedia > 0)
                        <a href="{{ route('peminjam.ajukan', $alat->id) }}" class="bg-[#062c21] hover:bg-emerald-600 text-white w-14 h-14 rounded-2xl shadow-lg transition-all flex items-center justify-center active:scale-95 shadow-emerald-900/20">
                            <i class="fas fa-plus text-lg"></i>
                        </a>
                    @else
                        <button class="bg-gray-100 text-gray-300 cursor-not-allowed w-14 h-14 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-ban text-lg"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <i class="fas fa-search text-gray-200 text-6xl mb-4"></i>
            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Alat tidak ditemukan</p>
        </div>
        @endforelse
    </div>
@endsection