<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    // Menampilkan semua wilayah
    public function index()
    {
        $wilayahs = Wilayah::all(); // Mengambil semua data wilayah
        return view('wilayah.index', compact('wilayahs'));
    }

    // Form untuk menambah wilayah baru
    public function create()
    {
        return view('wilayah.create');
    }

    // Menyimpan wilayah baru ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_wilayah' => 'required|string|max:255',
        ]);

        // Menyimpan data wilayah ke database
        Wilayah::create($request->all());
        return redirect()->route('wilayah.index');
    }

    // Form untuk mengedit wilayah
    public function edit(Wilayah $wilayah)
    {
        return view('wilayah.edit', compact('wilayah'));
    }

    // Mengupdate data wilayah
    public function update(Request $request, Wilayah $wilayah)
    {
        // Validasi input dari form
        $request->validate([
            'nama_wilayah' => 'required|string|max:255',
        ]);

        // Mengupdate data wilayah
        $wilayah->update($request->all());
        return redirect()->route('wilayah.index');
    }

    // Menghapus wilayah
    public function destroy(Wilayah $wilayah)
    {
        $wilayah->delete(); // Menghapus data wilayah
        return redirect()->route('wilayah.index');
    }
}
