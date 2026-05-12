<?php

namespace App\Http\Controllers;

use App\Models\PengurusBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;

class PengurusBlesscomnController extends Controller
{
    // Menampilkan daftar pengurus blesscomn
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $pengurus = $this->buildIndexQuery($request)->latest()->get();

        return view('pengurus_blesscomn.index', compact('pengurus', 'wilayahs', 'pelayanans'));
    }

    // Form untuk menambah pengurus blesscomn baru
    public function create(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        return view('pengurus_blesscomn.create', compact('wilayahs', 'pelayanans'));
    }

    // Menyimpan pengurus blesscomn baru ke database
    public function store(Request $request)
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'id_wilayah');

        $validated = $request->validate([
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

        $pengurusBlesscomn = PengurusBlesscomn::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Pengurus Blesscomn berhasil ditambahkan.',
                'data' => [
                    'id' => $pengurusBlesscomn->id,
                    'nama_ketua' => $pengurusBlesscomn->nama_ketua,
                    'no_wa_ketua' => $pengurusBlesscomn->no_wa_ketua,
                    'id_wilayah' => $pengurusBlesscomn->id_wilayah,
                    'id_pelayanan' => $pengurusBlesscomn->id_pelayanan,
                    'nama_asisten' => $pengurusBlesscomn->nama_asisten,
                    'no_wa_asisten' => $pengurusBlesscomn->no_wa_asisten,
                ],
            ], 201);
        }

        return redirect()->route('pengurus_blesscomn.index')
            ->with('success', 'Data Pengurus Blesscomn berhasil ditambahkan.');
    }

    // Form untuk mengedit pengurus blesscomn
    public function edit(Request $request, PengurusBlesscomn $pengurusBlesscomn)
    {
        $this->abortIfOutsideRegion($request, $pengurusBlesscomn->id_wilayah);

        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        return view('pengurus_blesscomn.edit', compact('pengurusBlesscomn', 'wilayahs', 'pelayanans'));
    }

    // Mengupdate data pengurus blesscomn
    public function update(Request $request, PengurusBlesscomn $pengurusBlesscomn)
    {
        $this->abortIfOutsideRegion($request, $pengurusBlesscomn->id_wilayah);
        $this->dataScope()->injectRegionIntoRequest($request, 'id_wilayah');

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
        $this->abortIfOutsideRegion(request(), $pengurusBlesscomn->id_wilayah);

        $pengurusBlesscomn->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengurus Blesscomn berhasil dihapus.']);
        }

        return redirect()->route('pengurus_blesscomn.index')
            ->with('success', 'Pengurus Blesscomn berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $this->validatedIds($request);
        $query = PengurusBlesscomn::whereIn('id', $ids);
        $this->dataScope()->applyToRequestQuery($query, $request, 'id_wilayah');
        $pengurus = $query->get();

        abort_if($pengurus->count() !== count($ids), 403);

        $pengurus->each->delete();

        $message = $pengurus->count().' data Pengurus Blesscomn berhasil dihapus.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('pengurus_blesscomn.index')->with('success', $message);
    }

    /**
     * Export daftar Pengurus Blesscomn ke CSV atau Excel.
     */
    public function export(Request $request)
    {
        $pengurus = $this->buildIndexQuery($request)->latest()->get();

        return SimpleTableExporter::download(
            'pengurus_blesscomn',
            ['Tanggal Input', 'Nama Ketua', 'No. WA Ketua', 'Nama Asisten', 'No. WA Asisten', 'Wilayah', 'Pelayanan'],
            $pengurus,
            fn (PengurusBlesscomn $item) => [
                optional($item->created_at)->format('Y-m-d'),
                $item->nama_ketua,
                $item->no_wa_ketua,
                $item->nama_asisten,
                $item->no_wa_asisten,
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
            ],
            $request->get('format', 'csv')
        );
    }

    /**
     * Query builder untuk daftar Pengurus Blesscomn.
     */
    private function buildIndexQuery(Request $request)
    {
        $query = PengurusBlesscomn::with(['wilayah', 'pelayanan']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('id_wilayah')) {
            $query->where('id_wilayah', $request->id_wilayah);
        }
        if ($request->filled('id_pelayanan')) {
            $query->where('id_pelayanan', $request->id_pelayanan);
        }

        $this->dataScope()->applyToRequestQuery($query, $request, 'id_wilayah');

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nama_ketua', 'like', "%{$search}%")
                    ->orWhere('no_wa_ketua', 'like', "%{$search}%")
                    ->orWhere('nama_asisten', 'like', "%{$search}%")
                    ->orWhere('no_wa_asisten', 'like', "%{$search}%")
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    private function dataScope(): DataScope
    {
        return app(DataScope::class);
    }

    private function validatedIds(Request $request): array
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:pengurus_blesscomns,id'],
        ]);

        return array_map('intval', $validated['ids']);
    }

    private function abortIfOutsideRegion(Request $request, int|string|null $wilayahId): void
    {
        $scopedWilayahId = $this->dataScope()->scopedWilayahId($request->user());

        abort_if($scopedWilayahId !== null && (int) $wilayahId !== $scopedWilayahId, 403);
    }
}
