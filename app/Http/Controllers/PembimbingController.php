<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
   // Menampilkan daftar pembimbing
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $pembimbings = $this->buildIndexQuery($request)->latest()->get();

        return view('pembimbing.index', compact('pembimbings', 'wilayahs', 'pelayanans'));
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
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembimbing tidak bisa dihapus karena masih dipakai oleh Anak Bimbingan.',
                ], 422);
            }

            return redirect()->route('pembimbing.index')
                ->withErrors(['delete' => 'Pembimbing tidak bisa dihapus karena masih dipakai oleh Anak Bimbingan.']);
        }

        $pembimbing->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pembimbing berhasil dihapus.']);
        }

        return redirect()->route('pembimbing.index')
            ->with('success', 'Pembimbing berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $this->validatedIds($request);
        $query = Pembimbing::whereIn('id', $ids);
        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');
        $pembimbings = $query->get();

        abort_if($pembimbings->count() !== count($ids), 403);

        $blocked = $pembimbings->filter(fn (Pembimbing $pembimbing) => $pembimbing->anakBimbingans()->exists());
        if ($blocked->isNotEmpty()) {
            return $this->bulkDeleteError(
                $request,
                'Beberapa pembimbing tidak bisa dihapus karena masih dipakai oleh Anak Bimbingan: '.$blocked->pluck('nama_pembimbing')->join(', ')
            );
        }

        $pembimbings->each->delete();

        return $this->bulkDeleteSuccess($request, $pembimbings->count(), 'Pembimbing');
    }

    public function export(Request $request)
    {
        $pembimbings = $this->buildIndexQuery($request)->latest()->get();

        return SimpleTableExporter::download(
            'pembimbing',
            ['Tanggal Input', 'Nama Pembimbing', 'Wilayah', 'Pelayanan'],
            $pembimbings,
            fn (Pembimbing $item) => [
                optional($item->created_at)->format('Y-m-d'),
                $item->nama_pembimbing,
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
            ],
            $request->get('format', 'csv')
        );
    }

    private function buildIndexQuery(Request $request)
    {
        $query = Pembimbing::with(['wilayah', 'pelayanan']);

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
                $subQuery->where('nama_pembimbing', 'like', "%{$search}%")
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
            'ids.*' => ['integer', 'distinct', 'exists:pembimbings,id'],
        ]);

        return array_map('intval', $validated['ids']);
    }

    private function bulkDeleteError(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return redirect()->route('pembimbing.index')
            ->withErrors(['delete' => $message]);
    }

    private function bulkDeleteSuccess(Request $request, int $count, string $label)
    {
        $message = "{$count} data {$label} berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('pembimbing.index')->with('success', $message);
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
