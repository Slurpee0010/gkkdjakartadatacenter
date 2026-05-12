<?php

namespace App\Http\Controllers;

use App\Models\AnakBimbingan;
use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnakBimbinganController extends Controller
{
     // Menampilkan daftar anak bimbingan
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $anakBimbingans = $this->buildIndexQuery($request)->latest()->get();

        return view('anak_bimbingan.index', compact('anakBimbingans', 'wilayahs', 'pelayanans'));
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
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak Bimbingan tidak bisa dihapus karena masih dipakai oleh Laporan PA.',
                ], 422);
            }

            return redirect()->route('anak_bimbingan.index')
                ->withErrors(['delete' => 'Anak Bimbingan tidak bisa dihapus karena masih dipakai oleh Laporan PA.']);
        }

        $anakBimbingan->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Anak Bimbingan berhasil dihapus.']);
        }

        return redirect()->route('anak_bimbingan.index')
            ->with('success', 'Anak Bimbingan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $this->validatedIds($request);
        $query = AnakBimbingan::whereIn('id', $ids);
        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');
        $anakBimbingans = $query->get();

        abort_if($anakBimbingans->count() !== count($ids), 403);

        $blocked = $anakBimbingans->filter(fn (AnakBimbingan $anakBimbingan) => $anakBimbingan->laporanPas()->exists());
        if ($blocked->isNotEmpty()) {
            return $this->bulkDeleteError(
                $request,
                'Beberapa Anak Bimbingan tidak bisa dihapus karena masih dipakai oleh Laporan PA: '.$blocked->pluck('nama_anak')->join(', ')
            );
        }

        $anakBimbingans->each->delete();

        return $this->bulkDeleteSuccess($request, $anakBimbingans->count(), 'Anak Bimbingan');
    }

    public function export(Request $request)
    {
        $anakBimbingans = $this->buildIndexQuery($request)->latest()->get();

        return SimpleTableExporter::download(
            'anak_bimbingan',
            ['Tanggal Input', 'Nama Anak', 'Pembimbing', 'Wilayah', 'Pelayanan'],
            $anakBimbingans,
            fn (AnakBimbingan $item) => [
                optional($item->created_at)->format('Y-m-d'),
                $item->nama_anak,
                $item->pembimbing->nama_pembimbing ?? '-',
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
            ],
            $request->get('format', 'csv')
        );
    }

    private function buildIndexQuery(Request $request)
    {
        $query = AnakBimbingan::with(['pembimbing', 'wilayah', 'pelayanan']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('pelayanan_id')) {
            $query->where('pelayanan_id', $request->pelayanan_id);
        }

        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nama_anak', 'like', "%{$search}%")
                    ->orWhereHas('pembimbing', fn ($relation) => $relation->where('nama_pembimbing', 'like', "%{$search}%"))
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    private function validatedIds(Request $request): array
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:anak_bimbingans,id'],
        ]);

        return array_map('intval', $validated['ids']);
    }

    private function bulkDeleteError(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return redirect()->route('anak_bimbingan.index')
            ->withErrors(['delete' => $message]);
    }

    private function bulkDeleteSuccess(Request $request, int $count, string $label)
    {
        $message = "{$count} data {$label} berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('anak_bimbingan.index')->with('success', $message);
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
