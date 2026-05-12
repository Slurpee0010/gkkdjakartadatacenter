<?php

namespace App\Http\Controllers;

use App\Models\AnakBimbingan;
use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Services\Rbac\DataScope;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnakBimbinganController extends Controller
{
     // Menampilkan daftar anak bimbingan
    public function index(Request $request)
    {
        // Mengambil semua data anak bimbingan dengan relasi ke pembimbing, wilayah, dan pelayanan
        $query = AnakBimbingan::with(['pembimbing', 'wilayah', 'pelayanan']);
        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');
        $anakBimbingans = $query->get();

        return view('anak_bimbingan.index', compact('anakBimbingans'));
    }

    // Form untuk menambah anak bimbingan baru
    public function create(Request $request)
    {
        $pembimbingQuery = Pembimbing::query();
        $this->dataScope()->applyToRequestQuery($pembimbingQuery, $request, 'wilayah_id');

        $pembimbings = $pembimbingQuery->get();
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::all();
        return view('anak_bimbingan.create', compact('pembimbings', 'wilayahs', 'pelayanans'));
    }

    // Menyimpan anak bimbingan baru ke database
    public function store(Request $request)
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        $this->validatePembimbingRelation($request);

        AnakBimbingan::create($request->all());

        return redirect()->route('anak_bimbingan.index');
    }

    // Form untuk mengedit anak bimbingan
    public function edit(Request $request, AnakBimbingan $anakBimbingan)
    {
        $this->abortIfOutsideRegion($request, $anakBimbingan->wilayah_id);

        $pembimbingQuery = Pembimbing::query();
        $this->dataScope()->applyToRequestQuery($pembimbingQuery, $request, 'wilayah_id');

        $pembimbings = $pembimbingQuery->get();
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::all();
        return view('anak_bimbingan.edit', compact('anakBimbingan', 'pembimbings', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data anak bimbingan
    public function update(Request $request, AnakBimbingan $anakBimbingan)
    {
        $this->abortIfOutsideRegion($request, $anakBimbingan->wilayah_id);
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

        $request->validate([
            'nama_anak' => 'required|string|max:255',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'pelayanan_id' => 'required|exists:pelayanans,id',
        ]);

        $this->validatePembimbingRelation($request);

        $anakBimbingan->update($request->all());

        return redirect()->route('anak_bimbingan.index');
    }

    // Menghapus anak bimbingan
    public function destroy(AnakBimbingan $anakBimbingan)
    {
        $this->abortIfOutsideRegion(request(), $anakBimbingan->wilayah_id);

        if ($anakBimbingan->laporanPas()->exists()) {
            return redirect()->route('anak_bimbingan.index')
                ->withErrors(['delete' => 'Anak Bimbingan tidak bisa dihapus karena masih dipakai oleh Laporan PA.']);
        }

        $anakBimbingan->delete();

        return redirect()->route('anak_bimbingan.index')
            ->with('success', 'Anak Bimbingan berhasil dihapus.');
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

    private function validatePembimbingRelation(Request $request): void
    {
        $pembimbing = Pembimbing::find($request->pembimbing_id);

        if (! $pembimbing
            || (int) $pembimbing->wilayah_id !== (int) $request->wilayah_id
            || (int) $pembimbing->pelayanan_id !== (int) $request->pelayanan_id) {
            throw ValidationException::withMessages([
                'pembimbing_id' => 'Pembimbing tidak sesuai dengan wilayah dan pelayanan.',
            ]);
        }
    }
}
