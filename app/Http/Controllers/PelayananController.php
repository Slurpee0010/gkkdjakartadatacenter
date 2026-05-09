<?php

namespace App\Http\Controllers;

use App\Models\Pelayanan;
use Illuminate\Http\Request;

class PelayananController extends Controller
{
   // Menampilkan daftar pelayanan
    public function index()
    {
        $pelayanans = Pelayanan::all(); // Mendapatkan semua data pelayanan
        return view('pelayanan.index', compact('pelayanans'));
    }

    // Form untuk menambah pelayanan
    public function create()
    {
        return view('pelayanan.create');
    }

    public function store(Request $request)
{
    // Validasi input dari form
    $request->validate([
        'nama_pelayanan' => 'required|string|max:255',
    ]);

    // Menyimpan data pelayanan
    Pelayanan::create($request->all());  // Hanya data yang diizinkan, tanpa _token
    return redirect()->route('pelayanan.index');
}

    // Form untuk mengedit pelayanan
    public function edit(Pelayanan $pelayanan)
    {
        return view('pelayanan.edit', compact('pelayanan'));
    }

    // Mengupdate data pelayanan
    public function update(Request $request, Pelayanan $pelayanan)
    {
        // Validasi input dari form
        $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
        ]);

        // Mengupdate data pelayanan
        $pelayanan->update($request->all());
        return redirect()->route('pelayanan.index');
    }

    // Menghapus pelayanan
    public function destroy(Pelayanan $pelayanan)
    {
        // Menghapus pelayanan
        $pelayanan->delete();
        return redirect()->route('pelayanan.index');
    }
}
