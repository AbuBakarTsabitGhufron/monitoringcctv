<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GlobalResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ApiUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();

            if ($users->isEmpty()) {
                return new GlobalResource(false, 'Belum ada data pengguna yang tersedia.', null);
            }

            return new GlobalResource(true, 'Data pengguna berhasil dimuat.', $users);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat memuat data pengguna.', null);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,pengguna',
            ]);

            if ($validator->fails()) {
                return new GlobalResource(false, 'Data yang Anda masukkan tidak valid.', $validator->errors());
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return new GlobalResource(true, 'Pengguna baru berhasil ditambahkan.', $user);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat menambahkan pengguna.', null);
        }
    }

    public function show(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return new GlobalResource(false, 'Data pengguna tidak ditemukan.', null);
            }

            return new GlobalResource(true, 'Detail data pengguna berhasil dimuat.', $user);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat memuat data pengguna.', null);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return new GlobalResource(false, 'Data pengguna tidak ditemukan.', null);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8',
                'role' => 'required|in:admin,pengguna',
            ]);

            if ($validator->fails()) {
                return new GlobalResource(false, 'Data yang Anda masukkan tidak valid.', $validator->errors());
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role, 
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return new GlobalResource(true, 'Data pengguna berhasil diperbarui.', $user);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return new GlobalResource(false, 'Email sudah digunakan. Silakan gunakan email lain.', null);
            }

            return new GlobalResource(false, 'Terjadi kesalahan saat memperbarui data pengguna.', null);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return new GlobalResource(false, 'Data pengguna tidak ditemukan.', null);
            }

            $user->delete();

            return new GlobalResource(true, 'Data pengguna berhasil dihapus.', null);
        } catch (\Exception $e) {
            return new GlobalResource(false, 'Terjadi kesalahan saat menghapus data pengguna.', null);
        }
    }
}
