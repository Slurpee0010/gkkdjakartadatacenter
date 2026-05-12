<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Services\Rbac\DataScope;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
   // Menampilkan daftar pembimbing
    public function index(Request $request)
    {
        // Mengambil semua data pembimbing dengan relasi ke wilayah dan pelayanan
        $query = Pembimbing::with(['wilayah', 'pelayanan']);
        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');
        $pembimbings = $query->get();

        return view('pembimbing.index', compact('pembimbings'));
    }

    // Form untuk menambah pembimbing baru
    public function create(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::all();
        return view('pembimbing.create', compact('wilayahs', 'pelayanans'));
    }

    // Menyimpan pembimbing baru ke database
    public function store(Request $request)
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

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
    public function edit(Request $request, Pembimbing $pembimbing)
    {
        $this->abortIfOutsideRegion($request, $pembimbing->wilayah_id);

        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::all();
        return view('pembimbing.edit', compact('pembimbing', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data pembimbing
    public function update(Request $request, Pembimbing $pembimbing)
    {
        $this->abortIfOutsideRegion($request, $pembimbing->wilayah_id);
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

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
        $this->abortIfOutsideRegion(request(), $pembimbing->wilayah_id);

        if ($pembimbing->anakBimbingans()->exists()) {
            return redirect()->route('pembimbing.index')
                ->withErrors(['delete' => 'Pembimbing tidak bisa dihapus karena masih dipakai oleh Anak Bimbingan.']);
        }

        $pembimbing->delete();

        return redirect()->route('pembimbing.index')
            ->with('success', 'Pembimbing berhasil dihapus.');
    }

    private function dataScope(): DataScope
    {
        return app(DataScope::class);
    }

    private function abortIfOutsideRegion(Request $request, int|string|null $wilayahId): void
    {
        $scopedWilayahId = $this->dataScope()->scopedWilayahId($request->user());

        abort_if($scopedWilayahId !== null && (int) $wilayahId !== $scopedWilayahId, 403);
    }
}
