<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $usedBy = $this->usedBy($wilayah);

        if ($usedBy !== []) {
            return redirect()->route('wilayah.index')
                ->withErrors([
                    'delete' => 'Wilayah tidak bisa dihapus karena masih dipakai oleh: '.implode(', ', $usedBy).'.',
                ]);
        }

        $wilayah->delete();

        return redirect()->route('wilayah.index')
            ->with('success', 'Wilayah berhasil dihapus.');
    }

    private function usedBy(Wilayah $wilayah): array
    {
        $references = [
            'User' => ['users', 'wilayah_id'],
            'Pembimbing' => ['pembimbings', 'wilayah_id'],
            'Anak Bimbingan' => ['anak_bimbingans', 'wilayah_id'],
            'Laporan PA' => ['laporan_pas', 'wilayah_id'],
            'Pengurus Blesscomn' => ['pengurus_blesscomns', 'id_wilayah'],
            'Master Blesscomn' => ['master_blesscomns', 'id_wilayah'],
            'Laporan Blesscomn' => ['laporan_blesscomns', 'id_wilayah'],
            'Kehadiran Ibadah' => ['kehadiran_ibadah', 'id_wilayah'],
        ];

        return collect($references)
            ->filter(fn (array $reference) => DB::table($reference[0])
                ->where($reference[1], $wilayah->id)
                ->exists())
            ->keys()
            ->all();
    }
}
