@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl shadow-sm animate-fade-in">
    <i class="fas fa-check-circle text-xl"></i>
    <p class="font-bold text-sm">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-600 px-6 py-4 rounded-2xl shadow-sm animate-fade-in">
    <i class="fas fa-exclamation-circle text-xl"></i>
    <p class="font-bold text-sm">{{ session('error') }}</p>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-rose-50 border border-rose-200 text-rose-600 px-6 py-4 rounded-2xl shadow-sm animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <i class="fas fa-exclamation-triangle text-xl"></i>
        <p class="font-bold text-sm">Ada kesalahan input:</p>
    </div>
    <ul class="list-disc list-inside text-xs font-medium ml-8">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Database Member Atlet</h1>
        <p class="text-gray-500">Kelola data atlet yang terdaftar di sistem SportRent.</p>
    </div>
    <button onclick="toggleModal('modal-tambah')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-emerald-200 transition-all active:scale-95">
        <i class="fas fa-user-plus"></i> Tambah Member Baru
    </button>
</div>

<div class="bg-white rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-center w-20">No</th>
                <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Atlet / Member</th>
                <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
                <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($users as $user)
            <tr class="hover:bg-emerald-50/30 transition-colors group">
                <td class="px-6 py-4 text-center font-bold text-gray-400 group-hover:text-emerald-600">
                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=10b981&color=fff" class="w-10 h-10 rounded-xl" alt="Avatar">
                        <div>
                            <p class="font-bold text-gray-900 leading-none mb-1">{{ $user->name }}</p>
                            <p class="text-xs text-emerald-600 font-medium uppercase">{{ $user->role }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-gray-700">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400">{{ $user->no_hp ?? '-' }}</p>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->no_hp }}', '{{ $user->role }}')" 
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus member ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="px-6 py-5 bg-gray-50/50 flex items-center justify-between">
        <p class="text-sm text-gray-500 font-medium">
            Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} Member
        </p>
        <div class="flex gap-2">
            <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold {{ $users->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-900' }}">Kembali</a>
            <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold {{ !$users->hasMorePages() ? 'text-gray-300 pointer-events-none' : 'text-gray-900' }}">Selanjutnya</a>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div id="modal-tambah" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-[#062c21]/60 backdrop-blur-sm" onclick="toggleModal('modal-tambah')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-10 relative z-10 shadow-2xl">
        <h2 class="text-2xl font-black text-slate-900 mb-6 uppercase italic">Tambah <span class="text-emerald-500">Member</span></h2>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 outline-none transition-all text-sm font-semibold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" name="email" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">No. HP</label>
                    <input type="text" name="no_hp" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 outline-none text-sm font-semibold">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <input type="password" name="password" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Role</label>
                <select name="role" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 outline-none text-sm font-semibold">
                    <option value="peminjam">Peminjam</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('modal-tambah')" class="flex-1 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400">Batal</button>
                <button type="submit" class="flex-[2] bg-[#062c21] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg">Simpan Member</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div id="modal-edit" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('modal-edit')"></div>
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-10 relative z-10 shadow-2xl">
        <h2 class="text-2xl font-black text-slate-900 mb-6 uppercase italic">Edit <span class="text-blue-500">Member</span></h2>
        <form id="form-edit" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            {{-- ID akan diisi oleh JavaScript --}}
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none text-sm font-semibold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" name="email" id="edit-email" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none text-sm font-semibold">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">No. HP</label>
                    <input type="text" name="no_hp" id="edit-nohp" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none text-sm font-semibold">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Role</label>
                <select name="role" id="edit-role" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none text-sm font-semibold">
                    <option value="peminjam">Peminjam</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password (Kosongkan jika tidak ganti)</label>
                <input type="password" name="password" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none text-sm font-semibold">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('modal-edit')" class="flex-1 py-4 font-black text-[10px] uppercase tracking-widest text-slate-400">Batal</button>
                <button type="submit" class="flex-[2] bg-blue-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg">Update Data</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>

<script>
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        if (modal) {
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }
    }

    function openEditModal(id, name, email, nohp, role) {
        // Set action URL dengan ID yang benar
        const form = document.getElementById('form-edit');
        form.action = '/admin/users/' + id;
        
        // Isi data ke input
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-nohp').value = nohp || '';
        document.getElementById('edit-role').value = role || 'peminjam';
        
        toggleModal('modal-edit');
    }
</script>
@endsection