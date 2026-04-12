@extends('layouts.peminjam')

@section('title', 'Dashboard')

@section('content')
    <div class="bg-gradient-to-r from-emerald-600 to-[#062c21] rounded-[2.5rem] p-8 mb-10 text-white shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-4xl font-extrabold italic tracking-tighter mb-2">Halo, <span class="text-emerald-200">{{ Auth::user()->name }}!</span></h1>
                <p class="text-emerald-100/80 text-sm">Selamat datang kembali di SportRent. Yuk cek alat yang tersedia!</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('peminjam.katalog') }}" class="bg-white text-emerald-700 px-6 py-3 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-emerald-50 transition-all shadow-lg flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i> Mulai Peminjaman
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] p-6 border border-gray-100 card-shadow">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-extrabold text-gray-900 uppercase italic tracking-tighter">Peminjaman <span class="text-emerald-600">Aktif</span></h2>
            <a href="{{ route('peminjam.kembali') }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-widest">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        @if(isset($peminjamanAktifList) && count($peminjamanAktifList) > 0)
            <div class="space-y-4">
                @foreach($peminjamanAktifList as $item)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center overflow-hidden">
                            @if($item->alat->foto)
                                <img src="{{ asset('storage/' . $item->alat->foto) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-volleyball-ball text-emerald-600 text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $item->alat->nama_alat }}</p>
                            <p class="text-[10px] text-gray-400">Kode: PJM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }} | Jumlah: {{ $item->jumlah }} unit</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-500 uppercase">Batas Kembali</p>
                        <p class="text-sm font-black {{ \Carbon\Carbon::now()->gt($item->tgl_kembali) ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <i class="fas fa-inbox text-gray-200 text-5xl mb-3"></i>
                <p class="text-gray-400 font-bold text-sm">Belum ada peminjaman aktif</p>
                <a href="{{ route('peminjam.katalog') }}" class="inline-block mt-3 text-emerald-600 text-xs font-bold uppercase tracking-wider">
                    Mulai Pinjam Sekarang →
                </a>
            </div>
        @endif
    </div>
@endsection