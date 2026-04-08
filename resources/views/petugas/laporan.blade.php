<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan | SportRent Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none; }
            .print-only { display: block; }
            body { background: white; p: 0; }
            .card-report { border: none; shadow: none; }
        }

        .sidebar-active {
            background: linear-gradient(to right, #10b981, #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">

    <aside class="w-72 bg-[#062c21] text-white flex flex-col h-screen shadow-2xl">
        <div class="p-8 flex items-center gap-3">
            <div class="bg-emerald-500 p-2 rounded-xl rotate-3 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-running text-white text-xl"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight italic">SPORT<span class="text-emerald-400">RENT</span></span>
        </div>

        <nav class="flex-1 px-6 space-y-2">
            <p class="text-[10px] font-bold text-emerald-500/50 uppercase tracking-[0.2em] mb-4">Staff Menu</p>
            
            <a href="/petugas/dashboard" 
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('petugas/dashboard') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                <i class="fas fa-chart-pie w-5"></i> Dashboard
            </a>

            <div class="pt-6">
                <p class="text-[10px] font-bold text-emerald-500/50 uppercase tracking-[0.2em] mb-4">Transaksi & Laporan</p>
                
                <a href="/petugas/menyetujui_peminjaman" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all mb-2 {{ Request::is('petugas/menyetujui_peminjaman') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-clipboard-check w-5"></i> Menyetujui Pinjam
                </a>
                
                <a href="/petugas/menyetujui_pengembalian" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all mb-2 {{ Request::is('petugas/menyetujui_pengembalian') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-file-import w-5"></i> Menyetujui Kembali
                </a>

                <a href="/petugas/laporan" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('petugas/laporan') ? 'sidebar-active text-white' : 'hover:bg-white/10 text-emerald-100/70 hover:text-white' }}">
                    <i class="fas fa-print w-5"></i> Cetak Laporan
                </a>
            </div>
        </nav>

        <div class="p-6 border-t border-emerald-900/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-xl transition-all font-bold text-sm shadow-lg shadow-orange-900/20">
                    <i class="fas fa-power-off"></i> LOGOUT
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10">
        
        <div class="no-print flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic uppercase">Laporan <span class="text-emerald-600">Aktivitas</span></h1>
                <p class="text-gray-500 font-medium uppercase text-[10px] tracking-widest mt-1 italic">Export data peminjaman untuk arsip fisik</p>
            </div>
        </div>

        <div class="no-print bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm mb-10">
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest italic mb-6">Konfigurasi Laporan</h2>
            
            <form action="{{ route('petugas.laporan.pdf') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-xs font-bold uppercase italic">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-xs font-bold uppercase italic">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-[#062c21] hover:bg-emerald-800 text-white font-black py-3.5 rounded-2xl shadow-xl shadow-emerald-900/10 transition-all flex items-center justify-center gap-3 text-xs uppercase tracking-widest">
                            <i class="fas fa-file-pdf"></i> EXPORT PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-report bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="flex flex-col items-center justify-center text-center border-b-2 border-double border-gray-200 pb-8 mb-8">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter italic leading-none">REKAPITULASI PEMINJAMAN ALAT</h3>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.3em] mt-2 italic">SPORTRENT MANAGEMENT SYSTEM - 2026</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                            <th class="pb-5 px-2">No</th>
                            <th class="pb-5">Tanggal</th>
                            <th class="pb-5">Peminjam</th>
                            <th class="pb-5">Alat Olahraga</th>
                            <th class="pb-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($laporans as $index => $data)
                        <tr>
                            <td class="py-5 px-2 text-xs font-bold text-gray-400 italic">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-5 text-xs font-bold text-gray-900 italic">
                                {{ $data->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-5">
                                <p class="text-xs font-black text-gray-900 uppercase italic">{{ $data->user->name }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $data->user->role ?? 'Peminjam' }}</p>
                            </td>
                            <td class="py-5 text-xs font-bold text-gray-600">
                                {{ $data->alat->nama_alat }} ({{ $data->jumlah }}x)
                            </td>
                            <td class="py-5 text-center">
                                @if($data->status == 'kembali' || $data->status == 'selesai')
                                    <span class="text-[9px] font-black border border-emerald-500 text-emerald-600 px-3 py-1 rounded-full uppercase italic tracking-tighter">Selesai</span>
                                @elseif($data->status == 'pinjam')
                                    <span class="text-[9px] font-black border border-orange-500 text-orange-600 px-3 py-1 rounded-full uppercase italic tracking-tighter">Dipinjam</span>
                                @else
                                    <span class="text-[9px] font-black border border-gray-400 text-gray-500 px-3 py-1 rounded-full uppercase italic tracking-tighter">{{ $data->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 italic uppercase text-[10px] tracking-widest">
                                Tidak ada data peminjaman ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-16 flex justify-end">
                <div class="text-center w-64">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic mb-16">Petugas Lapangan,</p>
                    <p class="text-xs font-black text-gray-900 uppercase italic underline leading-none">Staff SportRent</p>
                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase italic tracking-widest">NIP. 19200402 2026 01</p>
                </div>
            </div>
        </div>

    </main>
</div>

</body>
</html>