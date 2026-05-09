<?php

namespace App\Http\Controllers;

use App\Models\PengurusBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use Illuminate\Http\Request;

class PengurusBlesscomnController extends Controller
{
    // Menampilkan daftar pengurus blesscomn
    public function index()
    {
        $pengurus = PengurusBlesscomn::with(['wilayah', 'pelayanan'])->latest()->get();
        return view('pengurus_blesscomn.index', compact('pengurus'));
    }

    // Form untuk menambah pengurus blesscomn baru
    public function create()
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        return view('pengurus_blesscomn.create', compact('wilayahs', 'pelayanans'));
    }

    // Menyimpan pengurus blesscomn baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_ketua'    => 'required|string|max:255',
            'no_wa_ketua'   => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'id_wilayah'    => 'required|exists:wilayahs,id',
            'id_pelayanan'  => 'required|exists:pelayanans,id',
            'nama_asisten'  => 'required|string|max:255',
            'no_wa_asisten' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
        ], [
            'no_wa_ketua.regex'   => 'Format nomor WA Ketua tidak valid. Contoh: 08123456789',
            'no_wa_asisten.regex' => 'Format nomor WA Asisten tidak valid. Contoh: 08123456789',
        ]);

        PengurusBlesscomn::create($request->only([
            'nama_ketua', 'no_wa_ketua', 'id_wilayah', 'id_pelayanan', 'nama_asisten', 'no_wa_asisten',
        ]));

        return redirect()->route('pengurus_blesscomn.index')
            ->with('success', 'Data Pengurus Blesscomn berhasil ditambahkan.');
    }

    // Form untuk mengedit pengurus blesscomn
    public function edit(PengurusBlesscomn $pengurusBlesscomn)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        return view('pengurus_blesscomn.edit', compact('pengurusBlesscomn', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data pengurus blesscomn
    public function update(Request $request, PengurusBlesscomn $pengurusBlesscomn)
    {
        $request->validate([
            'nama_ketua'    => 'required|string|max:255',
            'no_wa_ketua'   => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'id_wilayah'    => 'required|exists:wilayahs,id',
            'id_pelayanan'  => 'required|exists:pelayanans,id',
            'nama_asisten'  => 'required|string|max:255',
            'no_wa_asisten' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
        ], [
            'no_wa_ketua.regex'   => 'Format nomor WA Ketua tidak valid. Contoh: 08123456789',
            'no_wa_asisten.regex' => 'Format nomor WA Asisten tidak valid. Contoh: 08123456789',
        ]);

        $pengurusBlesscomn->update($request->only([
            'nama_ketua', 'no_wa_ketua', 'id_wilayah', 'id_pelayanan', 'nama_asisten', 'no_wa_asisten',
        ]));

        return redirect()->route('pengurus_blesscomn.index')
            ->with('success', 'Data Pengurus Blesscomn berhasil diperbarui.');
    }

    // Soft delete pengurus blesscomn via AJAX
    public function destroy(PengurusBlesscomn $pengurusBlesscomn)
    {
        $pengurusBlesscomn->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengurus Blesscomn berhasil dihapus.']);
        }

        return redirect()->route('pengurus_blesscomn.index')
            ->with('success', 'Pengurus Blesscomn berhasil dihapus.');
    }
}
