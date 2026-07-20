<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class SecurityPasswordResetController extends Controller
{
    // Langkah 1: form masukkan email
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    // Langkah 1: proses email, cari pertanyaan keamanannya
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->pertanyaan_keamanan) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan atau akun ini belum memiliki pertanyaan keamanan. Hubungi pemilik usaha.',
            ]);
        }

        $request->session()->put('reset_user_id', $user->id);

        return redirect()->route('password.security');
    }

    // Langkah 2: tampilkan pertanyaan keamanan
    public function showSecurity(Request $request)
    {
        $userId = $request->session()->get('reset_user_id');

        if (!$userId) {
            return redirect()->route('password.request');
        }

        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget('reset_user_id');
            return redirect()->route('password.request');
        }

        return Inertia::render('Auth/SecurityQuestion', [
            'pertanyaan' => $user->pertanyaan_keamanan,
        ]);
    }

    // Langkah 2: verifikasi jawaban, simpan password baru
    public function resetPassword(Request $request)
    {
        $userId = $request->session()->get('reset_user_id');

        if (!$userId) {
            return redirect()->route('password.request');
        }

        $user = User::find($userId);

        if (!$user) {
            $request->session()->forget('reset_user_id');
            return redirect()->route('password.request');
        }

        $request->validate([
            'jawaban_keamanan' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $jawabanCocok = Hash::check(
            strtolower(trim($request->jawaban_keamanan)),
            $user->jawaban_keamanan
        );

        if (!$jawabanCocok) {
            return back()->withErrors([
                'jawaban_keamanan' => 'Jawaban tidak sesuai. Coba lagi.',
            ]);
        }

        $user->update(['password' => $request->password]);
        $request->session()->forget('reset_user_id');

        return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan masuk dengan password baru.');
    }
}
