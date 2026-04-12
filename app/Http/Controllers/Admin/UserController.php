<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Peminjaman;

class UserController extends Controller
{
    public function users()
    {
        // Mengambil semua user yang rolenya bukan admin
        $users = User::whereIn('role', ['peminjam', 'petugas'])->paginate(10);
        return view('admin.kelola_user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'nullable|string|max:15',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:peminjam,petugas'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'peminjam',
        ]);

        return redirect()->route('admin.kelola_user')->with('success', 'Member berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'no_hp' => 'nullable|string|max:15',
            'role' => 'required|in:peminjam,petugas'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.kelola_user')->with('success', 'Data member diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cek apakah user sedang meminjam alat
        $sedangMeminjam = Peminjaman::where('user_id', $id)
            ->whereIn('status', ['disetujui', 'pending', 'dikembalikan'])
            ->exists();
            
        if ($sedangMeminjam) {
            return back()->with('error', 'Member sedang memiliki peminjaman aktif, tidak dapat dihapus!');
        }
        
        $user->delete();
        return redirect()->route('admin.kelola_user')->with('success', 'Member telah dihapus!');
    }
}