<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas | SportRent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight italic">Dashboard Petugas</h1>
                <p class="text-gray-500 font-medium italic">Pantau aktivitas peminjaman hari ini secara real-time.</p>
            </div>
            <div class="flex gap-3">
                <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
                    <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg text-xs">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-500 italic font-black text-xl">
                    <i class="fas fa-hourglass-start"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Menunggu Approval</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ str_pad($waitingApproval, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Data</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="bg-orange-50 p-4 rounded-2xl text-orange-500 italic font-black text-xl">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Alat Dipinjam</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ str_pad($alatDipinjam, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Unit</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="bg-blue-50 p-4 rounded-2xl text-blue-500 italic font-black text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Selesai Hari Ini</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ str_pad($selesaiHariIni, 2, '0', STR_PAD_LEFT) }} <span class="text-xs font-medium text-gray-300">Item</span></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold text-gray-900 italic uppercase tracking-tighter">Antrean Tugas Terbaru</h2>
                <span class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full uppercase italic">Segera Proses</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="pb-4 px-2">Atlet</th>
                            <th class="pb-4">Alat Olahraga</th>
                            <th class="pb-4">Jenis Tugas</th>
                            <th class="pb-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($antreanTugas as $tugas)
                        <tr class="group hover:bg-gray-50/50 transition-all">
                            <td class="py-5 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-[#062c21] rounded-xl flex items-center justify-center text-emerald-400 font-bold shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($tugas->user->name, 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-sm text-gray-900 uppercase italic">{{ $tugas->user->name }}</span>
                                </div>
                            </td>
                            <td class="py-5">
                                <span class="text-sm font-semibold text-gray-600 block">{{ $tugas->alat->nama_alat }}</span>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded italic">
                                    Qty: {{ $tugas->jumlah }}
                                </span>
                            </td>
                            <td class="py-5">
                                @if($tugas->status == 'pending')
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase italic border border-emerald-200">
                                        <i class="fas fa-arrow-up mr-1"></i> Peminjaman
                                    </span>
                                @else
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase italic border border-blue-200">
                                        <i class="fas fa-arrow-down mr-1"></i> Pengembalian
                                    </span>
                                @endif
                            </td>
                            <td class="py-5 text-center">
                                @if($tugas->status == 'pending')
                                    <a href="/petugas/menyetujui_peminjaman" 
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black px-6 py-2.5 rounded-xl transition-all shadow-md uppercase tracking-widest inline-block active:scale-95">
                                        Setujui
                                    </a>
                                @else
                                    <a href="/petugas/menyetujui_pengembalian" 
                                    class="bg-gray-900 hover:bg-emerald-800 text-white text-[10px] font-black px-6 py-2.5 rounded-xl transition-all shadow-md uppercase tracking-widest inline-block active:scale-95">
                                        Cek Alat
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-300">
                                    <i class="fas fa-tasks text-4xl mb-4"></i>
                                    <p class="text-xs font-black uppercase tracking-[0.2em] italic">Antrean Bersih / Kosong</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

</body>
</html>