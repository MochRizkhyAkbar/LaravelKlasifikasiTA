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
        // Pastikan relasi 'roles' didefinisikan dengan benar di Model User
        $users = User::with('roles')->get();
        return view('admin_dinas.manajemen_user', compact('users'));
    }

    /**
     * 2. Menyimpan user baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            // 'password' => 'required|min:6',
            'role' => 'required',
            'status' => 'required',
        ]);
        // dd($request);

        // 2. Simpan ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('Pass123'),
            // 'password' => Hash::make($request->password), // Gunakan Hash::make agar lebih standar
            'status' => $request->status,
        ]);

        // 3. Assign role via Spatie
        $user->assignRole($request->role);

        // Pastikan nama route ini sesuai dengan yang ada di routes/web.php Anda
        return redirect()->route('admin.manajemen.user')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * 3. Mengupdate data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->input('update_type') === 'status_only') {
            $user->update(['status' => $request->status]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required',
                'status' => 'required',
                'password' => 'nullable|string|min:6',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
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
