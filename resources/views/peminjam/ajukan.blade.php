@extends('layouts.peminjam')

@section('title', 'Form Peminjaman')

@section('content')
    <a href="{{ route('peminjam.katalog') }}" class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 uppercase tracking-widest mb-8 hover:gap-3 transition-all">
        <i class="fas fa-arrow-left"></i> Kembali ke Katalog
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- CARD INFO ALAT --}}
        <div class="lg:col-span-1">
            <div class="group bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm sticky top-28 transition-all hover:shadow-2xl">
                <div class="relative bg-slate-50 rounded-[2rem] aspect-square mb-6 flex items-center justify-center overflow-hidden shadow-inner">
                    @if($alat->foto)
                        <img src="{{ asset('storage/' . $alat->foto) }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                    @else
                        <i class="fas fa-running text-slate-200 text-7xl rotate-12 group-hover:scale-110 group-hover:text-emerald-500/20 transition-all duration-500"></i>
                    @endif
                </div>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1 text-center">{{ $alat->kategori }}</p>
                <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter text-center leading-tight">{{ $alat->nama_alat }}</h3>
                
                <div class="mt-6 space-y-3 border-t border-slate-50 pt-6">
                    <div class="flex justify-between text-[10px] font-bold uppercase italic">
                        <span class="text-slate-400">Tersedia</span>
                        <span class="text-slate-900">{{ $alat->stok_tersedia }} Unit</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold uppercase italic">
                        <span class="text-slate-400">Kondisi</span>
                        <span class="{{ $alat->kondisi == 'baik' ? 'text-emerald-600' : 'text-amber-500' }} underline capitalize">{{ $alat->kondisi }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM INPUT --}}
        <div class="lg:col-span-2">
            {{-- Alert Error --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-500 text-white rounded-[2rem] shadow-lg shadow-rose-200 flex items-center gap-3 animate-pulse">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-5 bg-rose-50 border-l-4 border-rose-500 rounded-r-[2rem]">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-rose-600 text-[10px] font-black uppercase tracking-tight flex items-center gap-2">
                                <i class="fas fa-times-circle"></i> {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl"></div>
                
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Formulir <span class="text-emerald-500">Peminjaman</span></h2>

                <form action="{{ route('peminjam.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="id_alat" value="{{ $alat->id }}">

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Jumlah Pinjam (Unit)</label>
                        <div class="relative">
                            <i class="fas fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input type="number" name="jumlah" min="1" max="{{ $alat->stok_tersedia }}" placeholder="Maksimal: {{ $alat->stok_tersedia }}" required
                                   class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Tgl Pinjam</label>
                            <input type="date" id="tgl_pinjam" name="tgl_pinjam" 
                                   value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" readonly
                                   class="w-full px-4 py-3.5 bg-slate-100 border border-slate-200 rounded-2xl outline-none font-semibold text-sm cursor-not-allowed opacity-70">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Tgl Kembali</label>
                            <input type="date" id="tgl_kembali" name="tgl_kembali" 
                                   min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required 
                                   class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Tujuan Peminjaman</label>
                        <textarea name="tujuan" rows="3" placeholder="Contoh: Latihan rutin untuk kompetisi..." required
                                  class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm resize-none"></textarea>
                    </div>

                    {{-- KETENTUAN BOX --}}
                    <div class="bg-amber-50 p-5 rounded-[2rem] border border-amber-100 flex gap-4">
                        <div class="bg-amber-500/10 p-3 rounded-2xl h-fit">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-amber-900 font-extrabold uppercase tracking-widest mb-2 italic">Ketentuan & Denda Sistem:</p>
                            <ul class="space-y-1.5 text-[10px] text-amber-800 font-bold uppercase italic">
                                <li class="flex items-center gap-2">
                                    <div class="w-1 h-1 bg-amber-400 rounded-full"></div> Durasi Maksimal: <span class="text-amber-900 font-black ml-1">3 Hari</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <div class="w-1 h-1 bg-amber-400 rounded-full"></div> Terlambat: <span class="text-amber-900 font-black ml-1 text-rose-600">Rp 5.000 / Hari</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <div class="w-1 h-1 bg-amber-400 rounded-full"></div> Kondisi Rusak/Hilang: <span class="text-amber-900 font-black ml-1">Denda sesuai kerusakan</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#062c21] hover:bg-emerald-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transition-all active:scale-95 text-xs tracking-[0.2em] uppercase italic">
                            KIRIM PERMINTAAN PINJAM <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglPinjamInput = document.getElementById('tgl_pinjam');
        const tglKembaliInput = document.getElementById('tgl_kembali');

        function updateConstraints() {
            if (tglPinjamInput.value) {
                let startDate = new Date(tglPinjamInput.value);
                
                // Hitung batas maksimal (3 hari setelah tgl pinjam)
                let maxDate = new Date(startDate);
                maxDate.setDate(startDate.getDate() + 3);

                // Set atribut min dan max pada input tgl_kembali
                tglKembaliInput.min = tglPinjamInput.value;
                tglKembaliInput.max = maxDate.toISOString().split('T')[0];
            }
        }

        updateConstraints();
    });
</script>
@endsection