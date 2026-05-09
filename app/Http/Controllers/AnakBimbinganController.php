<?php

namespace App\Http\Controllers;

use App\Models\AnakBimbingan;
use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use Illuminate\Http\Request;

class AnakBimbinganController extends Controller
{
     // Menampilkan daftar anak bimbingan
    public function index()
    {
        // Mengambil semua data anak bimbingan dengan relasi ke pembimbing, wilayah, dan pelayanan
        $anakBimbingans = AnakBimbingan::with(['pembimbing', 'wilayah', 'pelayanan'])->get();
        return view('anak_bimbingan.index', compact('anakBimbingans'));
    }

    // Form untuk menambah anak bimbingan baru
    public function create()
    {
        $pembimbings = Pembimbing::all();
        $wilayahs = Wilayah::all();
        $pelayanans = Pelayanan::all();
        return view('anak_bimbingan.create', compact('pembimbings', 'wilayahs', 'pelayanans'));
    }

    // Menyimpan anak bimbingan baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        AnakBimbingan::create($request->all());

        return redirect()->route('anak_bimbingan.index');
    }

    // Form untuk mengedit anak bimbingan
    public function edit(AnakBimbingan $anakBimbingan)
    {
        $pembimbings = Pembimbing::all();
        $wilayahs = Wilayah::all();
        $pelayanans = Pelayanan::all();
        return view('anak_bimbingan.edit', compact('anakBimbingan', 'pembimbings', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data anak bimbingan
    public function update(Request $request, AnakBimbingan $anakBimbingan)
    {
        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        $anakBimbingan->update($request->all());

        return redirect()->route('anak_bimbingan.index');
    }

    // Menghapus anak bimbingan
    public function destroy(AnakBimbingan $anakBimbingan)
    {
        $anakBimbingan->delete();

        return redirect()->route('anak_bimbingan.index');
    }
}
