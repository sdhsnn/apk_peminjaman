@extends('layouts.admin')

@section('title', 'Kelola Alat')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center gap-3">
    <i class="fas fa-check-circle text-xl"></i>
    <span class="font-bold text-sm uppercase tracking-wider">{{ session('success') }}</span>
</div>
@endif

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Katalog Peralatan</h1>
        <p class="text-gray-500 text-sm">Kelola stok, harga sewa, dan kondisi aset secara real-time.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <form action="{{ route('admin.alat') }}" method="GET" class="relative group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari alat..." 
                class="w-64 pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-xs focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all card-shadow font-semibold">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
        </form>

        <button onclick="toggleModal('modal-tambah')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-200 transition-all active:scale-95 text-sm">
            <i class="fas fa-plus-circle"></i> Tambah Alat
        </button>
    </div>
</div>

<!-- FILTER KATEGORI -->
<div class="flex items-center gap-3 mb-8 overflow-x-auto pb-2 no-scrollbar">
    @php 
        $categories = ['Semua' => 'fa-th-large', 'Bola' => 'fa-volleyball-ball', 'Raket' => 'fa-table-tennis', 'Fitness' => 'fa-dumbbell'];
    @endphp
    @foreach($categories as $cat => $icon)
        <a href="{{ route('admin.alat', ['kategori' => $cat, 'search' => request('search')]) }}" 
           class="px-6 py-2.5 rounded-full text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2
           {{ (request('kategori', 'Semua') == $cat) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
            <i class="fas {{ $icon }}"></i> {{ $cat }}
        </a>
    @endforeach
</div>

<!-- CARD GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    @forelse($alats as $alat)
    <div class="group bg-white rounded-[2.5rem] p-4 border border-gray-100 card-shadow hover:border-emerald-300 transition-all duration-300">
        <div class="relative h-48 w-full bg-gray-100 rounded-[2rem] overflow-hidden mb-4">
            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter shadow-sm z-10
                {{ $alat->kondisi == 'baik' ? 'text-emerald-600' : ($alat->kondisi == 'lecet' ? 'text-amber-500' : ($alat->kondisi == 'rusak' ? 'text-rose-600' : 'text-gray-500')) }}">
                {{ $alat->kondisi }}
            </span>
            
            <div class="w-full h-full flex items-center justify-center overflow-hidden">
                @if($alat->foto)
                    <img src="{{ asset('storage/' . $alat->foto) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @else
                    <div class="flex flex-col items-center">
                        <i class="fas fa-camera fa-3x text-gray-200"></i>
                        <p class="text-[9px] text-gray-300 mt-2 font-bold uppercase tracking-widest">No Image</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="px-2 pb-2">
            <div class="flex justify-between items-start mb-1">
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">{{ $alat->kategori }}</p>
                <p class="text-[9px] font-black text-gray-300 uppercase">Harga Asli: Rp {{ number_format($alat->harga_asli, 0, ',', '.') }}</p>
            </div>
            <h3 class="text-lg font-extrabold text-gray-900 leading-tight mb-1 truncate">{{ $alat->nama_alat }}</h3>
            
            <p class="text-emerald-600 font-black text-sm mb-2">
                Rp {{ number_format($alat->harga_sewa, 0, ',', '.') }}<span class="text-[10px] text-gray-400 font-normal"> / hari</span>
            </p>

            <p class="text-gray-400 text-xs line-clamp-2 mb-4 h-8 leading-relaxed">
                {{ $alat->deskripsi ?? 'Tidak ada deskripsi detail untuk alat ini.' }}
            </p>
            
            <div class="flex items-center justify-between py-3 border-t border-gray-50">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Stok Tersedia</p>
                    <p class="text-lg font-black text-gray-900">
                        {{ $alat->stok_tersedia }} <span class="text-xs font-medium text-gray-300">/{{ $alat->stok_total }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openEditAlat('{{ $alat->id }}', '{{ addslashes($alat->nama_alat) }}', '{{ $alat->kategori }}', '{{ $alat->stok_total }}', '{{ $alat->kondisi }}', '{{ addslashes($alat->deskripsi) }}', '{{ $alat->foto ? asset('storage/'.$alat->foto) : '' }}', '{{ (int)$alat->harga_sewa }}', '{{ (int)$alat->harga_asli }}')" 
                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-center">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" onsubmit="return confirm('Hapus alat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center">
        <div class="flex flex-col items-center">
            <i class="fas fa-box-open text-5xl text-gray-200 mb-4"></i>
            <p class="text-gray-400 font-bold uppercase italic tracking-widest text-xs">Tidak ada data alat ditemukan.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- MODAL TAMBAH ALAT -->
<div id="modal-tambah" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-[#062c21]/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modal-tambah')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-8 relative z-10 shadow-2xl transition-all overflow-y-auto max-h-[90vh]">
        <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Tambah <span class="text-emerald-500">Alat Baru</span></h2>
        <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Peralatan</label>
                <input type="text" name="nama_alat" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none font-semibold text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                    <select name="kategori" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-semibold">
                        <option value="Bola">Bola</option>
                        <option value="Raket">Raket</option>
                        <option value="Fitness">Fitness</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kondisi Awal</label>
                    <select name="kondisi" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-semibold">
                        <option value="baik">Baik</option>
                        <option value="lecet">Lecet</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Stok</label>
                    <input type="number" name="stok_total" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sewa/Hari</label>
                    <input type="number" name="harga_sewa" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Harga Beli</label>
                    <input type="number" name="harga_asli" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none text-sm font-medium resize-none"></textarea>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Upload Foto</label>
                <input type="file" name="foto" accept="image/*" class="w-full px-5 py-2 bg-slate-50 border border-dashed border-slate-300 rounded-2xl text-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('modal-tambah')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50">Batal</button>
                <button type="submit" class="flex-[2] bg-[#062c21] text-white px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-900/20 hover:bg-emerald-800 transition-all">Simpan Katalog</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT ALAT -->
<div id="modal-edit" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modal-edit')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-8 relative z-10 shadow-2xl transition-all overflow-y-auto max-h-[90vh]">
        <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Edit <span class="text-blue-500">Data Alat</span></h2>
        <form id="form-edit" action="#" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            
            <div class="flex justify-center mb-2">
                <img id="edit-preview" src="" class="h-24 w-24 object-cover rounded-2xl border-4 border-slate-50 shadow-sm hidden">
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Peralatan</label>
                <input type="text" id="edit-nama" name="nama_alat" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none font-semibold text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                    <select id="edit-kategori" name="kategori" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                        <option value="Bola">Bola</option>
                        <option value="Raket">Raket</option>
                        <option value="Fitness">Fitness</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kondisi</label>
                    <select id="edit-kondisi" name="kondisi" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                        <option value="baik">Baik</option>
                        <option value="lecet">Lecet</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Stok</label>
                    <input type="number" id="edit-stok" name="stok_total" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sewa</label>
                    <input type="number" id="edit-harga" name="harga_sewa" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Beli</label>
                    <input type="number" id="edit-harga-asli" name="harga_asli" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="2" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-medium resize-none"></textarea>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Ganti Foto</label>
                <input type="file" name="foto" accept="image/*" class="w-full px-5 py-2 bg-slate-50 border border-dashed border-slate-300 rounded-2xl text-sm file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('modal-edit')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">Batal</button>
                <button type="submit" class="flex-[2] bg-blue-600 text-white px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-900/20 hover:bg-blue-700 transition-all">Update Data</button>
            </div>
        </form>
    </div>
</div>

<style>
    .card-shadow { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
</style>

<script>
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        if (modal) {
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }
    }

    function openEditAlat(id, nama, kategori, stok, kondisi, deskripsi, fotoUrl, hargaSewa, hargaAsli) {
        document.getElementById('form-edit').action = `/admin/alat/${id}`;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-kategori').value = kategori;
        document.getElementById('edit-stok').value = stok;
        document.getElementById('edit-kondisi').value = kondisi;
        document.getElementById('edit-deskripsi').value = deskripsi;
        document.getElementById('edit-harga').value = hargaSewa;
        document.getElementById('edit-harga-asli').value = hargaAsli;
        
        const preview = document.getElementById('edit-preview');
        if(fotoUrl) {
            preview.src = fotoUrl;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }

        toggleModal('modal-edit');
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(m => {
                m.classList.add('hidden');
                m.classList.remove('flex');
            });
        }
    });
</script>
@endsection