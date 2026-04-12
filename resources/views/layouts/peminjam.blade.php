<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SportRent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-shadow { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        @yield('extra-style')
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

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
                <a href="{{ route('peminjam.dashboard') }}" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('peminjam.dashboard') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('peminjam.katalog') }}" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('peminjam.katalog') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
                    Katalog
                </a>
                <a href="{{ route('peminjam.kembali') }}" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('peminjam.kembali') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
                    Pengembalian
                </a>
                <a href="{{ route('peminjam.riwayat') }}" 
                   class="text-xs font-black uppercase tracking-widest transition-all {{ request()->routeIs('peminjam.riwayat') ? 'text-emerald-400 border-b-2 border-emerald-400 pb-1' : 'text-emerald-100/50 hover:text-white' }}">
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

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-6 py-10">
        @yield('content')
    </main>

    @yield('extra-script')
</body>
</html>