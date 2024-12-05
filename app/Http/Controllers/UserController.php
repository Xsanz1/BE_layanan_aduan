<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function view()
    {
        return User::all(); // Mengembalikan semua pengguna
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required',
            'foto' => 'nullable|file', // Validasi foto
        ]);

        // Handle file upload if a photo is provided
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            if ($fotoPath && file_exists(public_path($fotoPath))) {
                unlink(public_path($fotoPath)); // Hapus file lama
            }
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }
        // Persiapkan data pengguna
        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password), // Enkripsi password
            'role' => $request->role,
            'foto' => $fotoPath

        ];

        // Buat pengguna baru
        $user = User::create($userData);

        return response()->json($user, 200);
    }

    public function show($id_user)
    {
        return User::findOrFail($id_user);
    }

    public function update(Request $request, $id_user)
    {
        $user = User::find($id_user);
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404); // Not found response
        }
    
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            // unlink('storage/'.$fotoPath);
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }
    
        $user->name = $request->name;
        $user->username = $request->username;
        $user ->role = $request->role;
        $user->foto = $fotoPath;
        
        $user->save(); // Simpan perubahan
    
        return response()->json($user);
    }

    public function destroy(string $id_user)
    {
        $user = User::find($id_user);
        if (!$user) {
            return response()->json(['message' => 'user tidak ditemukan.'], 404); // Not found response
        }
        
        $fotoPath = $user->foto;
        unlink('storage/'.$fotoPath);

        $user->delete();
        return response()->json(['message' => 'user berhasil dihapus.']);
    }
}
