<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Kembali | SportRent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-active {
            background: linear-gradient(to right, #10b981, #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">
    <aside class="w-72 bg-[#062c21] text-white flex flex-col shadow-2xl">
        <div class="p-8 flex items-center gap-3">
            <div class="bg-emerald-500 p-2 rounded-xl rotate-3 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-running text-white text-xl"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight italic">SPORT<span class="text-emerald-400">RENT</span></span>
        </div>

        <nav class="flex-1 px-6 space-y-2">
            <p class="text-[10px] font-bold text-emerald-500/50 uppercase tracking-[0.2em] mb-4">Staff Menu</p>
            
            <a href="{{ route('petugas.dashboard') }}" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('petugas/dashboard*') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                <i class="fas fa-chart-pie w-5"></i> Dashboard
            </a>

            <div class="pt-6">
                <p class="text-[10px] font-bold text-emerald-500/50 uppercase tracking-[0.2em] mb-4">Transaksi & Laporan</p>
                
                <a href="{{ route('petugas.menyetujui_peminjaman') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all mb-2 {{ Request::is('petugas/menyetujui_peminjaman*') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-clipboard-check w-5"></i> Menyetujui Pinjam
                </a>
                
                <a href="{{ route('petugas.menyetujui_kembali') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all mb-2 {{ Request::is('petugas/menyetujui_kembali*') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-file-import w-5"></i> Menyetujui Kembali
                </a>

                <a href="{{ route('petugas.laporan') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('petugas/laporan*') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-print w-5"></i> Cetak Laporan
                </a>
            </div>
        </nav>
        
        <div class="p-6 border-t border-emerald-900/50 bg-emerald-950/20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-xl transition-all font-bold text-sm shadow-lg shadow-orange-900/20 active:scale-95">
                    <i class="fas fa-power-off"></i> LOGOUT
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10 no-scrollbar">
        
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center gap-3 animate-pulse">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold text-sm uppercase tracking-wider">{{ session('success') }}</span>
        </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic uppercase">Persetujuan <span class="text-emerald-600">Kembali</span></h1>
                <p class="text-gray-500 font-medium uppercase text-[10px] tracking-widest mt-1">Verifikasi kondisi fisik & hitung denda otomatis</p>
            </div>
            <div class="flex gap-3">
                <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    <span class="text-xs font-black text-blue-700 uppercase tracking-widest">{{ $pengembalians->count() }} Alat Perlu Dicek</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                            <th class="pb-5 px-4">Alat Olahraga</th>
                            <th class="pb-5 px-4">Peminjam</th> 
                            <th class="pb-5 px-4 text-center">Pilih Kondisi Fisik</th>
                            <th class="pb-5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pengembalians as $data)
                            <tr class="group hover:bg-gray-50/50 transition-all text-sm">
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 shrink-0 rounded-2xl overflow-hidden border-2 border-white shadow-sm bg-gray-50">
                                            @if($data->alat && $data->alat->foto)
                                                <img src="{{ asset('storage/' . $data->alat->foto) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-emerald-200">
                                                    <i class="fas fa-volleyball-ball text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 leading-none mb-1 text-sm">{{ $data->alat->nama_alat }}</p>
                                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md tracking-widest">
                                                #TRX-{{ $data->id }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-6 px-4">
                                    <span class="font-black text-gray-900 uppercase tracking-tight block leading-none mb-1">
                                        {{ $data->user->name }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 font-bold italic uppercase tracking-wider">{{ $data->jumlah }} Unit Alat</span>
                                </td>
                                
                                <form action="{{ route('petugas.kembali.proses', $data->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <td class="py-6 px-4">
                                        <div class="flex justify-center gap-2">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="kondisi" value="baik" class="hidden peer" checked>
                                                <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                                    Baik
                                                </div>
                                            </label>
                                            
                                            <label class="cursor-pointer">
                                                <input type="radio" name="kondisi" value="lecet" class="hidden peer">
                                                <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-yellow-400 peer-checked:text-white peer-checked:border-yellow-400 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                                    Lecet
                                                </div>
                                            </label>

                                            <label class="cursor-pointer">
                                                <input type="radio" name="kondisi" value="rusak" class="hidden peer">
                                                <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                                    Rusak
                                                </div>
                                            </label>

                                            <label class="cursor-pointer">
                                                <input type="radio" name="kondisi" value="hilang" class="hidden peer">
                                                <div class="text-[9px] font-black border border-gray-100 px-4 py-2 rounded-xl text-gray-400 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all uppercase tracking-tighter hover:bg-gray-50 shadow-sm">
                                                    Hilang
                                                </div>
                                            </label>
                                        </div>
                                    </td>

                                    <td class="py-6 px-4 text-center">
                                        <button type="submit" class="bg-[#062c21] hover:bg-emerald-800 text-white text-[10px] font-black px-6 py-3 rounded-2xl transition-all shadow-lg shadow-emerald-900/10 uppercase tracking-widest active:scale-95">
                                            Konfirmasi
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-32 text-center px-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                                            <i class="fas fa-check-double text-emerald-500 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-black uppercase italic tracking-[0.3em] text-[10px]">Gudang Clear! Tidak ada antrian cek alat.</p>
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
                    <span>Verifikasi kondisi fisik menentukan denda akhir</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-gray-300"></i>
                    <span>Sistem mencatat waktu real-time</span>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>