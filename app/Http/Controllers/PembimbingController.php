<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
   // Menampilkan daftar pembimbing
    public function index()
    {
        // Mengambil semua data pembimbing dengan relasi ke wilayah dan pelayanan
        $pembimbings = Pembimbing::with(['wilayah', 'pelayanan'])->get();
        return view('pembimbing.index', compact('pembimbings'));
    }

    // Form untuk menambah pembimbing baru
    public function create()
    {
        $wilayahs = Wilayah::all();
        $pelayanans = Pelayanan::all();
        return view('pembimbing.create', compact('wilayahs', 'pelayanans'));
    }

    // Menyimpan pembimbing baru ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_pembimbing' => 'required|string|max:255',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        // Menyimpan data pembimbing ke database
        Pembimbing::create([
            'nama_pembimbing' => $request->nama_pembimbing,
            'wilayah_id' => $request->wilayah_id,
            'pelayanan_id' => $request->pelayanan_id,
        ]);

        return redirect()->route('pembimbing.index');
    }

    // Form untuk mengedit pembimbing
    public function edit(Pembimbing $pembimbing)
    {
        $wilayahs = Wilayah::all();
        $pelayanans = Pelayanan::all();
        return view('pembimbing.edit', compact('pembimbing', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data pembimbing
    public function update(Request $request, Pembimbing $pembimbing)
    {
        $request->validate([
            'nama_pembimbing' => 'required|string|max:255',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        // Mengupdate data pembimbing
        $pembimbing->update([
            'nama_pembimbing' => $request->nama_pembimbing,
            'wilayah_id' => $request->wilayah_id,
            'pelayanan_id' => $request->pelayanan_id,
        ]);

        return redirect()->route('pembimbing.index');
    }

    // Menghapus pembimbing
    public function destroy(Pembimbing $pembimbing)
    {
        $pembimbing->delete();

        return redirect()->route('pembimbing.index');
    }
}
