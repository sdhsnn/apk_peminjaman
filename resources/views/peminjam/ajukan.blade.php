<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman | SportRent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">

    <nav class="bg-[#062c21] text-white sticky top-0 z-50 shadow-xl">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-500 p-2 rounded-lg rotate-3 shadow-lg">
                    <i class="fas fa-running text-white"></i>
                </div>
                <span class="text-xl font-black italic tracking-tighter uppercase">SPORT<span class="text-emerald-400">RENT</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="/peminjam/dashboard" class="text-xs font-black uppercase tracking-widest text-emerald-100/50 hover:text-white transition-all">Katalog</a>
                <a href="/peminjam/pengembalian" class="text-xs font-black uppercase tracking-widest text-emerald-100/50 hover:text-white transition-all">Riwayat</a>
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

    <main class="max-w-7xl mx-auto px-6 py-12">
        
        <a href="/peminjam/dashboard" class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 uppercase tracking-widest mb-8 hover:gap-3 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="group bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm sticky top-28 transition-all hover:shadow-2xl">
                    <div class="relative bg-slate-50 rounded-[2rem] aspect-square mb-6 flex items-center justify-center overflow-hidden">
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

            <div class="lg:col-span-2">
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

                    <form action="{{ route('peminjam.pengajuan.store') }}" method="POST" class="space-y-6">
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
                                <input type="date" 
                                    id="tgl_pinjam"
                                    name="tgl_pinjam" 
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" 
                                    readonly
                                    class="w-full px-4 py-3.5 bg-slate-100 border border-slate-200 rounded-2xl outline-none font-semibold text-sm cursor-not-allowed opacity-70">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Tgl Kembali</label>
                                <input type="date" 
                                    id="tgl_kembali"
                                    name="tgl_kembali" 
                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" 
                                    required 
                                    class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Tujuan Peminjaman</label>
                            <textarea name="tujuan" rows="3" placeholder="Contoh: Latihan rutin untuk kompetisi..." required
                                      class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm resize-none"></textarea>
                        </div>

                        <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100 flex gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                            <div>
                                <p class="text-[10px] text-amber-800 font-bold uppercase italic">Ketentuan Sistem:</p>
                                <ul class="text-[10px] text-amber-700 leading-relaxed font-semibold uppercase italic list-disc ml-4">
                                    <li>Maksimal durasi peminjaman adalah <span class="font-black text-amber-900">3 Hari</span>.</li>
                                    <li>Denda sebesar <span class="font-black text-amber-900 underline">Rp 5.000 / hari</span> jika terlambat.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-[#062c21] hover:bg-emerald-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transition-all active:scale-95 text-xs tracking-[0.2em] uppercase">
                                KIRIM PERMINTAAN PINJAM
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

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

            // Jalankan saat load pertama kali
            updateConstraints();
        });
    </script>

</body>
</html>