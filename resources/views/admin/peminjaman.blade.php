@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase italic">Transaksi <span class="text-emerald-600">Sewa Alat</span></h1>
        <p class="text-gray-500 font-medium">Kelola peminjaman alat yang sedang berlangsung.</p>
    </div>
    <button onclick="toggleModal('modal-tambah-pinjam')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-emerald-200 transition-all active:scale-95">
        <i class="fas fa-plus-circle"></i> Input Peminjaman Baru
    </button>
</div>

<div class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 mb-6">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atlet atau alat..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold">
    </div>
</div>

<div class="bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-36">Kode Pinjam</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Atlet / Peminjam</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alat & Jumlah</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batas Kembali</th>
                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="tableBody">
            @forelse($peminjamanBerlangsung as $item)
            <tr class="hover:bg-emerald-50/30 transition-colors group">
                <td class="px-6 py-4 text-center">
                    <span class="font-bold text-gray-900 text-sm bg-gray-100 px-2 py-1 rounded-lg">
                        PJM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-900 flex items-center justify-center text-white font-bold shadow-sm">
                            {{ substr($item->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 leading-none mb-1">{{ $item->user->name }}</p>
                            @if($item->status == 'disetujui')
                                <span class="text-[9px] bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-md font-black uppercase tracking-wider">Aktif</span>
                            @elseif($item->status == 'pending')
                                <span class="text-[9px] bg-amber-100 text-amber-600 px-2 py-0.5 rounded-md font-black uppercase tracking-wider">Pending</span>
                            @elseif($item->status == 'selesai' || $item->status == 'dikembalikan')
                                <span class="text-[9px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-md font-black uppercase tracking-wider">Selesai</span>
                            @else
                                <span class="text-[9px] bg-rose-100 text-rose-600 px-2 py-0.5 rounded-md font-black uppercase tracking-wider">Ditolak</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-black text-gray-700 uppercase italic leading-none mb-1">{{ $item->alat->nama_alat }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Qty: {{ $item->jumlah }} Unit</p>
                </td>
                <td class="px-6 py-4 text-xs font-semibold text-gray-600">
                    <div class="flex flex-col">
                        <span class="font-bold tracking-tight"><i class="fas fa-calendar-alt text-emerald-500 mr-1"></i> {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-end gap-2">
                        @if($item->status == 'pending')
                        <form action="{{ route('admin.peminjaman.verifikasi', $item->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="disetujui">
                            <button class="w-8 h-8 flex items-center justify-center bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-all shadow-sm">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.peminjaman.verifikasi', $item->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ditolak">
                            <button class="w-8 h-8 flex items-center justify-center bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-all shadow-sm">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                        @endif

                        @if($item->status == 'disetujui')
                        <button onclick="openReturnModal('{{ $item->id }}', '{{ $item->user->name }}')" class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded-lg hover:bg-blue-600 shadow-sm">
                            <i class="fas fa-file-import text-xs"></i>
                        </button>
                        @endif

                        <form action="{{ route('admin.peminjaman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data transaksi ini?')">
                            @csrf @method('DELETE')
                            <button class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-lg hover:bg-rose-100 hover:text-rose-600 transition-all">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-24 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-folder-open text-gray-200 text-xl"></i>
                        </div>
                        <p class="text-gray-400 font-bold uppercase italic tracking-widest text-[10px]">Belum ada data peminjaman saat ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-6 bg-gray-50/50 flex items-center justify-between border-t border-gray-100">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            Showing {{ $peminjamanBerlangsung->firstItem() ?? 0 }} to {{ $peminjamanBerlangsung->lastItem() ?? 0 }} of {{ $peminjamanBerlangsung->total() }} records
        </p>

        <div class="flex gap-2">
            @if ($peminjamanBerlangsung->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-200 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $peminjamanBerlangsung->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:bg-white hover:text-emerald-600 transition-all shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#062c21] text-white shadow-lg shadow-emerald-900/20 font-bold text-xs">
                {{ $peminjamanBerlangsung->currentPage() }}
            </span>

            @if ($peminjamanBerlangsung->hasMorePages())
                <a href="{{ $peminjamanBerlangsung->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:bg-white hover:text-emerald-600 transition-all shadow-sm">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-200 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PEMINJAMAN -->
<div id="modal-tambah-pinjam" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-[#062c21]/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modal-tambah-pinjam')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-10 relative z-10 shadow-2xl">
        <div class="flex justify-between items-start mb-8">
            <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">
                Input <span class="text-emerald-500 underline decoration-emerald-100">Pinjaman</span>
            </h2>
            <button onclick="toggleModal('modal-tambah-pinjam')" class="text-slate-300 hover:text-rose-500 transition-colors">
                <i class="fas fa-times-circle text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Member</label>
                <select name="user_id" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm">
                    <option value="">-- Cari Nama Atlet --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Alat</label>
                <select name="alat_id" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm">
                    <option value="">-- Nama Alat Olahraga --</option>
                    @foreach($alats as $alat)
                        <option value="{{ $alat->id }}">{{ $alat->nama_alat }} (Stok: {{ $alat->stok_tersedia }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jumlah</label>
                    <input type="number" name="jumlah" value="1" min="1" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-semibold text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal Kembali</label>
                    <input type="date" 
                        name="tgl_kembali" 
                        id="tgl_kembali"
                        required 
                        min="{{ date('Y-m-d') }}" 
                        max="{{ date('Y-m-d', strtotime('+3 days')) }}"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl font-semibold text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                </div>
            </div>
            
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tujuan Peminjaman</label>
                <textarea name="tujuan" rows="3" placeholder="Contoh: Latihan rutin untuk kompetisi..." required
                          class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm resize-none"></textarea>
            </div>
            
            <div class="flex gap-3 pt-6">
                <button type="button" onclick="toggleModal('modal-tambah-pinjam')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">Batal</button>
                <button type="submit" class="flex-[2] bg-[#062c21] text-white px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-900/20 hover:bg-emerald-800 transition-all italic">Proses Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI KEMBALI (DENGAN PILIHAN KONDISI) -->
<div id="modal-kembali" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-[#062c21]/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modal-kembali')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 relative z-10 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-import fa-2x"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Konfirmasi</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Alat milik <span id="m-member" class="text-emerald-600 font-bold"></span></p>
        </div>
        
        <form id="form-kembali" method="POST" action="">
            @csrf
            @method('PATCH')
            
            <div class="mb-5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kondisi Alat Saat Dikembalikan</label>
                <div class="flex flex-wrap gap-2 justify-center">
                    <label class="cursor-pointer">
                        <input type="radio" name="kondisi" value="baik" class="hidden peer" checked>
                        <div class="text-[9px] font-black border border-gray-200 px-4 py-2 rounded-xl text-gray-500 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition-all">
                            Baik
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="kondisi" value="lecet" class="hidden peer">
                        <div class="text-[9px] font-black border border-gray-200 px-4 py-2 rounded-xl text-gray-500 peer-checked:bg-yellow-400 peer-checked:text-white peer-checked:border-yellow-400 transition-all">
                            Lecet
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="kondisi" value="rusak" class="hidden peer">
                        <div class="text-[9px] font-black border border-gray-200 px-4 py-2 rounded-xl text-gray-500 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 transition-all">
                            Rusak
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="kondisi" value="hilang" class="hidden peer">
                        <div class="text-[9px] font-black border border-gray-200 px-4 py-2 rounded-xl text-gray-500 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all">
                            Hilang
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Catatan (Opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: Alat lecet di bagian samping..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none text-sm font-semibold resize-none"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="toggleModal('modal-kembali')" class="flex-1 px-4 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">Batal</button>
                <button type="submit" class="flex-[2] bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all italic">Konfirmasi Kembali</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }

    function openReturnModal(id, member) {
        document.getElementById('form-kembali').action = `/admin/peminjaman/kembalikan/${id}`;
        document.getElementById('m-member').innerText = member;
        toggleModal('modal-kembali');
    }

    // Live search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableBody tr');
        
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    const tglKembaliInput = document.getElementById('tgl_kembali');
    if (tglKembaliInput) {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 3);
        
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };
        
        tglKembaliInput.min = formatDate(today);
        tglKembaliInput.max = formatDate(maxDate);
    }
</script>
@endsection