<?php

namespace App\Http\Controllers;

use App\Models\DataMitra;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataMitraController extends Controller
{
    public function index()
    {
        return Inertia::render('DataMitra/Index', [
            'dataMitra' => DataMitra::latest()->get(),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
        ]);
        $validated['user_id'] = $request->user()->id;
        DataMitra::create($validated);
        return redirect()->route('data-mitra.index')->with('success', 'Data mitra berhasil ditambahkan.');
    }
    public function update(Request $request, DataMitra $dataMitra)
    {
        $validated = $request->validate([
            'cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
        ]);
        $dataMitra->update($validated);
        return redirect()->route('data-mitra.index')->with('success', 'Data mitra berhasil diperbarui.');
    }
    public function destroy(DataMitra $dataMitra)
    {
        $dataMitra->delete();
        return redirect()->route('data-mitra.index')->with('success', 'Data mitra berhasil dihapus.');
    }
}
