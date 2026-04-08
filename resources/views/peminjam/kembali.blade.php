<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman | SportRent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-slate-50">

    {{-- NAVBAR --}}
    <nav class="bg-[#062c21] text-white sticky top-0 z-50 shadow-xl">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-500 p-2 rounded-lg rotate-3 shadow-lg">
                    <i class="fas fa-running text-white"></i>
                </div>
                <span class="text-xl font-black italic tracking-tighter uppercase">SPORT<span class="text-emerald-400">RENT</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="/peminjam/dashboard" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->is('peminjam/dashboard*') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
                   Katalog
                </a>

                <a href="/peminjam/pengembalian" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->is('peminjam/pengembalian*') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
                   Riwayat
                </a>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-black text-emerald-400 uppercase leading-none">Atlet</p>
                    <p class="text-xs font-bold text-white uppercase tracking-tight">{{ Auth::user()->name }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center font-black shadow-lg border-2 border-emerald-400/20 uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="w-10 h-10 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl transition-all flex items-center justify-center">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 italic uppercase tracking-tighter">Status <span class="text-emerald-600">Peminjaman</span></h1>
                <p class="text-slate-500 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Pantau batas waktu dan denda alat yang sedang dipinjam</p>
            </div>
            @if(session('success'))
                <div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl text-[10px] font-bold uppercase animate-bounce">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($peminjamans as $pinjam)
                @php
                    // 1. Paksa kedua tanggal ke awal hari (00:00:00) agar jam tidak merusak hitungan
                    $deadline = \Carbon\Carbon::parse($pinjam->tgl_kembali)->startOfDay();
                    $hariIni = now()->startOfDay(); 
                    
                    // 2. Cek apakah sudah lewat hari (Greater Than)
                    $isOverdue = $hariIni->gt($deadline);
                    $totalDenda = 0;

                    if($isOverdue) {
                        // 3. Gunakan diffInDays dan paksa ke integer (angka bulat)
                        // Tanpa startOfDay(), selisih 1 jam bisa dianggap 1.1 hari yang bikin denda jadi 6.944
                        $selisihHari = (int) $hariIni->diffInDays($deadline);
                        $totalDenda = $selisihHari * 5000; 
                    }
                @endphp

                <div class="bg-white rounded-[2.5rem] p-6 border {{ $isOverdue ? 'border-rose-100' : 'border-slate-100' }} card-shadow hover:shadow-xl transition-all group">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        
                        <div class="w-32 h-32 {{ $isOverdue ? 'bg-rose-50' : 'bg-slate-50' }} rounded-[2rem] flex items-center justify-center shrink-0 relative overflow-hidden">
                            @if($pinjam->alat->foto)
                                <img src="{{ asset('storage/' . $pinjam->alat->foto) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-volleyball-ball {{ $isOverdue ? 'text-rose-500/20' : 'text-emerald-500/20' }} text-5xl rotate-12 transition-transform"></i>
                            @endif
                            <div class="absolute bottom-3 {{ $isOverdue ? 'bg-rose-500 text-white' : 'bg-emerald-100 text-emerald-700' }} text-[8px] font-black px-2 py-0.5 rounded-full uppercase italic">
                                {{ $isOverdue ? 'TERLAMBAT' : $pinjam->status }}
                            </div>
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">{{ $pinjam->alat->kategori }}</p>
                            <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter leading-tight">
                                {{ $pinjam->alat->nama_alat }}
                            </h3>
                            
                            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-6">
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Qty</p>
                                    <p class="text-sm font-black text-slate-900 uppercase italic">{{ $pinjam->jumlah }} Unit</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Batas Kembali</p>
                                    <p class="text-sm font-black uppercase italic {{ $isOverdue ? 'text-rose-500' : 'text-emerald-600' }}">
                                        {{ $deadline->format('d M Y') }}
                                    </p>
                                </div>
                                @if($isOverdue)
                                <div>
                                    <p class="text-[9px] font-bold text-rose-400 uppercase tracking-widest">Denda Berjalan</p>
                                    <p class="text-sm font-black text-rose-600 uppercase italic">
                                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 w-full md:w-auto">
                            <form action="{{ route('peminjam.proses_kembali', $pinjam->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian alat?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="w-full md:w-auto {{ $isOverdue ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#062c21] hover:bg-emerald-800' }} text-white font-black px-8 py-4 rounded-2xl shadow-xl transition-all active:scale-95 text-[10px] tracking-[0.2em] uppercase">
                                    KEMBALIKAN
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-24 bg-white rounded-[3rem] border-2 border-dashed border-slate-100 card-shadow">
                    <div class="bg-slate-50 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-slate-200 text-3xl"></i>
                    </div>
                    <h3 class="text-slate-900 font-black uppercase italic tracking-tighter text-xl">Tidak ada pinjaman</h3>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-2 text-center">Silakan pilih alat di katalog untuk mulai meminjam</p>
                    <a href="/peminjam/dashboard" class="inline-block mt-8 bg-emerald-500 text-white text-[10px] font-black px-8 py-4 rounded-xl shadow-lg hover:bg-emerald-600 transition-all uppercase tracking-widest">Ke Katalog</a>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="max-w-5xl mx-auto px-6 pb-12">
        <div class="bg-emerald-900/5 p-6 rounded-[2.5rem] border border-emerald-900/10 flex items-start gap-4">
            <div class="bg-emerald-600 text-white p-2 rounded-lg text-xs italic shadow-lg">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-800 uppercase tracking-[0.1em] mb-1 italic leading-none">Informasi Denda</p>
                <p class="text-[10px] text-emerald-700/70 font-bold leading-relaxed uppercase italic">
                    Keterlambatan pengembalian dikenakan denda Rp 5.000,- per hari per alat. Pastikan mengembalikan alat tepat waktu untuk menghindari akumulasi biaya.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>