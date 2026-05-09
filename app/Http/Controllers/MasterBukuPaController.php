<?php

namespace App\Http\Controllers;

use App\Models\MasterBukuPa;
use Illuminate\Http\Request;

class MasterBukuPaController extends Controller
{
    // Menampilkan daftar buku PA
    public function index()
    {
        $bukuPas = MasterBukuPa::orderBy('nama_buku')->get();
        return view('master_buku_pa.index', compact('bukuPas'));
    }

    // Form untuk menambah buku PA baru
    public function create()
    {
        return view('master_buku_pa.create');
    }

    // Menyimpan buku PA baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'jumlah_bab' => 'required|integer|min:1',
        ]);

        MasterBukuPa::create($request->only('nama_buku', 'jumlah_bab'));

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil ditambahkan.');
    }

    // Form untuk mengedit buku PA
    public function edit(MasterBukuPa $masterBukuPa)
    {
        return view('master_buku_pa.edit', compact('masterBukuPa'));
    }

    // Mengupdate data buku PA
    public function update(Request $request, MasterBukuPa $masterBukuPa)
    {
        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'jumlah_bab' => 'required|integer|min:1',
        ]);

        $masterBukuPa->update($request->only('nama_buku', 'jumlah_bab'));

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil diperbarui.');
    }

    /**
     * Ticket 4: Soft Delete buku PA via AJAX.
     * Returns JSON response for SweetAlert.
     */
    public function destroy(MasterBukuPa $masterBukuPa)
    {
        $masterBukuPa->delete(); // Soft delete karena model pakai SoftDeletes

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Buku PA berhasil dihapus.']);
        }

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil dihapus.');
    }

    /**
     * API: Mengembalikan detail buku PA (untuk AJAX).
     */
    public function show(MasterBukuPa $masterBukuPa)
    {
        return response()->json([
            'id' => $masterBukuPa->id,
            'nama_buku' => $masterBukuPa->nama_buku,
            'jumlah_bab' => $masterBukuPa->jumlah_bab,
        ]);
    }
}
