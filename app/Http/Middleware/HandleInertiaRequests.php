<?php

namespace App\Http\Middleware;

use App\Models\Produk;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'stokMenipisCount' => function () use ($request) {
                if (!$request->user() || $request->user()->peran !== 'pemilik_usaha') {
                    return 0;
                }
                return Produk::where('stok', '<', 10)->count();
            },
            'stokMenipisList' => function () use ($request) {
                if (!$request->user() || $request->user()->peran !== 'pemilik_usaha') {
                    return [];
                }
                return Produk::where('stok', '<', 10)
                    ->orderBy('stok')
                    ->limit(8)
                    ->get(['id', 'nama_produk', 'stok', 'satuan']);
            },
        ];
    }
}
