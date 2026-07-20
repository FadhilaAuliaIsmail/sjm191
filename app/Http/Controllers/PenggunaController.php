<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PenggunaController extends Controller
{
    public function index()
    {
        return Inertia::render('Pengguna/Index', [
            'pengguna' => User::latest()->get(),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:pengguna,email',
            'password' => 'required|string|min:8',
            'peran'    => 'required|in:pemilik_usaha,kasir',
        ]);
        User::create($validated);
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }
    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:8',
            'peran'    => 'required|in:pemilik_usaha,kasir',
        ]);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        $pengguna->update($validated);
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }
    public function destroy(Request $request, User $pengguna)
    {
        if ($pengguna->id === $request->user()->id) {
            return back()->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }
        $pengguna->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
