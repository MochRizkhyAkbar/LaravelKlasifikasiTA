<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManajemenUserController extends Controller
{
    /**
     * 1. Menampilkan daftar user
     */
    public function index()
    {
        // Memuat data user beserta relasi role-nya
        $users = User::with('roles')->get();
        return view('admin_dinas.manajemen_user', compact('users'));
    }

    /**
     * 2. Menyimpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required',
            'status' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.manajemen.user')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * 3. Mengupdate data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Menggunakan flag 'update_type' untuk membedakan aksi
        // Jika request berisi 'update_type' bernilai 'status_only', maka hanya update status
        if ($request->input('update_type') === 'status_only') {
            $user->update(['status' => $request->status]);
        } else {
            // Update profil lengkap
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required',
                'status' => 'required',
                'password' => 'nullable|string|min:8',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles($request->role);
        }

        return redirect()->route('admin.manajemen.user')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * 4. Menghapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.manajemen.user')->with('success', 'User berhasil dihapus!');
    }
}
