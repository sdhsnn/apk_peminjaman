@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex justify-between items-center mb-10">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Admin</h1>
        <p class="text-gray-500">Kelola peralatan olahraga dan aktivitas member hari ini.</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-bold text-gray-600 uppercase">System Online</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <div class="relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group">
        <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Member</p>
                <h3 class="text-4xl font-black mt-1 text-gray-900">{{ $totalMember }}</h3>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl text-blue-500 group-hover:scale-110 transition-transform">
                <i class="fas fa-users fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group">
        <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Alat</p>
                <h3 class="text-4xl font-black mt-1 text-gray-900">{{ $totalAlat }}</h3>
            </div>
            <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-500 group-hover:scale-110 transition-transform">
                <i class="fas fa-basketball-ball fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group">
        <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sewa Aktif</p>
                <h3 class="text-4xl font-black mt-1 text-gray-900">{{ $sewaAktif }}</h3>
            </div>
            <div class="bg-orange-50 p-4 rounded-2xl text-orange-500 group-hover:scale-110 transition-transform">
                <i class="fas fa-stopwatch fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group">
        <div class="absolute right-0 top-0 h-full w-1 bg-purple-500"></div>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dikembalikan</p>
                <h3 class="text-4xl font-black mt-1 text-gray-900">{{ $dikembalikan }}</h3>
            </div>
            <div class="bg-purple-50 p-4 rounded-2xl text-purple-500 group-hover:scale-110 transition-transform">
                <i class="fas fa-history fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="mt-10">
    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-chart-line text-emerald-500"></i> Statistik Peminjaman
                </h2>
                <p class="text-gray-400 text-xs font-medium mt-1">Analisis aktivitas penyewaan alat 7 hari terakhir</p>
            </div>
        </div>

        <div class="relative h-[400px]">
            <canvas id="loanChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
    const ctx = document.getElementById('loanChart').getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctx, {
        type: 'line', 
        data: {
            labels: @json($labels), 
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: @json($counts), 
                borderColor: '#10b981', 
                backgroundColor: gradient,
                borderWidth: 4,
                fill: true,
                tension: 0.4, 
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#062c21',
                    titleFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 13 },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return ` ${context.parsed.y} Peminjaman`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
                    ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans', weight: '600' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', weight: '600' } }
                }
            }
        }
    });
</script>
@endpush