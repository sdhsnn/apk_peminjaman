@extends('layouts.peminjam')

@section('title', 'Pengembalian Alat')

@section('content')
    {{-- HEADER --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 italic uppercase tracking-tighter leading-none">
                Pengembalian <span class="text-emerald-600">Alat</span>
            </h1>
            <p class="text-slate-500 text-[10px] font-bold mt-2 uppercase tracking-[0.2em]">Selesaikan peminjaman aktif dan cek denda Anda</p>
        </div>
        
        @if(session('success'))
            <div class="bg-emerald-500 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase italic shadow-lg shadow-emerald-200 animate-bounce">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- INFO PANEL --}}
    <div class="bg-amber-50 p-6 rounded-[2.5rem] border border-amber-100 flex flex-col md:flex-row gap-6 mb-10">
        <div class="bg-amber-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200 shrink-0">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <div>
                <p class="text-[10px] text-amber-800 font-black uppercase italic tracking-widest">Aturan Dasar</p>
                <p class="text-xs text-amber-700 font-bold uppercase italic mt-1">Maksimal pinjam 3 hari kerja.</p>
            </div>
            <div>
                <p class="text-[10px] text-amber-800 font-black uppercase italic tracking-widest">Denda Terlambat</p>
                <p class="text-xs text-amber-700 font-bold uppercase italic mt-1 text-rose-600">Rp 5.000 / Hari.</p>
            </div>
            <div>
                <p class="text-[10px] text-amber-800 font-black uppercase italic tracking-widest">Denda Kondisi</p>
                <p class="text-xs text-amber-700 font-bold uppercase italic mt-1">Rusak/Hilang sesuai harga alat.</p>
            </div>
        </div>
    </div>

    {{-- LIST ALAT AKTIF --}}
    <div class="space-y-6">
        @forelse($peminjamans as $pinjam)
            <div class="bg-white rounded-[3rem] p-8 border {{ $pinjam->estimasi_denda > 0 ? 'border-rose-200 bg-rose-50/10' : 'border-slate-100' }} card-shadow hover:shadow-2xl transition-all group overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-slate-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>

                <div class="flex flex-col lg:flex-row items-center gap-10 relative z-10">
                    <div class="w-40 h-40 {{ $pinjam->estimasi_denda > 0 ? 'bg-rose-100' : 'bg-emerald-50' }} rounded-[2.5rem] flex items-center justify-center shrink-0 shadow-inner group-hover:rotate-3 transition-transform">
                        @if($pinjam->alat->foto)
                            <img src="{{ asset('storage/' . $pinjam->alat->foto) }}" class="w-full h-full object-cover rounded-[2.5rem]">
                        @else
                            <i class="fas fa-volleyball-ball {{ $pinjam->estimasi_denda > 0 ? 'text-rose-500' : 'text-emerald-500' }} text-6xl opacity-20"></i>
                        @endif
                    </div>

                    <div class="flex-1 text-center lg:text-left">
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-3">
                            <span class="bg-slate-900 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase italic tracking-widest">
                                PJM-{{ str_pad($pinjam->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-1">{{ $pinjam->alat->nama_alat }}</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 italic">{{ $pinjam->alat->kategori }}</p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Jumlah</p>
                                <p class="text-sm font-black text-slate-800 uppercase italic">{{ $pinjam->jumlah }} Unit</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Batas Kembali</p>
                                <p class="text-sm font-black {{ $pinjam->estimasi_denda > 0 ? 'text-rose-600' : 'text-emerald-600' }} uppercase italic">
                                    {{ \Carbon\Carbon::parse($pinjam->tgl_kembali)->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Denda</p>
                                <p class="text-sm font-black {{ $pinjam->estimasi_denda > 0 ? 'text-rose-600' : 'text-slate-800' }} uppercase italic">
                                    Rp {{ number_format($pinjam->estimasi_denda, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Pembayaran</p>
                                <p class="text-sm font-black text-emerald-700 uppercase italic leading-none">Tunai ke Petugas</p>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 w-full lg:w-auto pt-6 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-10">
                        <form action="{{ route('peminjam.proses_kembali', $pinjam->id) }}" method="POST" onsubmit="return confirm('Sudah siap mengembalikan alat?')">
                            @csrf
                            <button type="submit" class="w-full lg:w-auto bg-[#062c21] hover:bg-emerald-600 text-white font-black px-10 py-5 rounded-[1.5rem] shadow-xl shadow-emerald-900/20 transition-all hover:-translate-y-1 active:scale-95 text-xs tracking-[0.2em] uppercase italic flex items-center justify-center gap-3">
                                Ajukan Kembali <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-32 bg-white rounded-[4rem] border-4 border-dashed border-slate-100 card-shadow">
                <i class="fas fa-check-double text-slate-200 text-4xl mb-6"></i>
                <h3 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Bebas Tugas!</h3>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-3 italic">Tidak ada alat yang perlu dikembalikan.</p>
            </div>
        @endforelse
    </div>
@endsection