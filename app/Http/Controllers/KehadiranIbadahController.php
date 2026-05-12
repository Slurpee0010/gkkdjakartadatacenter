<?php

namespace App\Http\Controllers;

use App\Models\KehadiranIbadah;
use App\Models\Pelayanan;
use App\Models\Wilayah;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KehadiranIbadahController extends Controller
{
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $ibadahs = $this->buildIndexQuery($request)
            ->latest('tanggal_ibadah')
            ->latest('created_at')
            ->get();

        return view('kehadiran_ibadah.index', compact('ibadahs', 'wilayahs', 'pelayanans'));
    }

    public function create(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $kehadiranIbadah = new KehadiranIbadah([
            'tanggal_ibadah' => Carbon::today()->toDateString(),
            'hadir_pria_onsite' => 0,
            'hadir_wanita_onsite' => 0,
            'hadir_pria_online' => 0,
            'hadir_wanita_online' => 0,
            'baru_pria' => 0,
            'baru_wanita' => 0,
            'is_nama_manual' => false,
        ]);

        return view('kehadiran_ibadah.create', compact('kehadiranIbadah', 'wilayahs', 'pelayanans'));
    }

    public function store(Request $request)
    {
        KehadiranIbadah::create($this->validatedPayload($request));

        return redirect()->route('kehadiran_ibadah.index')
            ->with('success', 'Data kehadiran ibadah berhasil disimpan.');
    }

    public function edit(Request $request, KehadiranIbadah $kehadiranIbadah)
    {
        $this->abortIfOutsideRegion($request, $kehadiranIbadah->id_wilayah);

        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();

        return view('kehadiran_ibadah.edit', compact('kehadiranIbadah', 'wilayahs', 'pelayanans'));
    }

    public function update(Request $request, KehadiranIbadah $kehadiranIbadah)
    {
        $this->abortIfOutsideRegion($request, $kehadiranIbadah->id_wilayah);

        $kehadiranIbadah->update($this->validatedPayload($request));

        return redirect()->route('kehadiran_ibadah.index')
            ->with('success', 'Data kehadiran ibadah berhasil diperbarui.');
    }

    public function destroy(KehadiranIbadah $kehadiranIbadah)
    {
        $this->abortIfOutsideRegion(request(), $kehadiranIbadah->id_wilayah);

        $kehadiranIbadah->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data kehadiran ibadah berhasil dihapus.']);
        }

        return redirect()->route('kehadiran_ibadah.index')
            ->with('success', 'Data kehadiran ibadah berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $ibadahs = $this->buildIndexQuery($request)
            ->latest('tanggal_ibadah')
            ->latest('created_at')
            ->get();

        return SimpleTableExporter::download(
            'kehadiran_ibadah',
            [
                'Tanggal',
                'Nama Ibadah',
                'Wilayah',
                'Pelayanan',
                'Pria Onsite',
                'Wanita Onsite',
                'Total Onsite',
                'Pria Online',
                'Wanita Online',
                'Total Online',
                'Baru Pria',
                'Baru Wanita',
                'Total Baru',
                'Grand Total',
            ],
            $ibadahs,
            fn (KehadiranIbadah $item) => [
                optional($item->tanggal_ibadah)->format('Y-m-d'),
                $item->nama_ibadah,
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
                $item->hadir_pria_onsite,
                $item->hadir_wanita_onsite,
                $item->total_hadir_onsite,
                $item->hadir_pria_online,
                $item->hadir_wanita_online,
                $item->total_hadir_online,
                $item->baru_pria,
                $item->baru_wanita,
                $item->total_baru,
                $item->grand_total,
            ],
            $request->get('format', 'csv')
        );
    }

    public function previewNamaIbadah(Request $request)
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'id_wilayah');

        $validated = $request->validate([
            'id_wilayah' => 'required|exists:wilayahs,id',
            'id_pelayanan' => 'required|exists:pelayanans,id',
        ]);

        $wilayah = Wilayah::findOrFail($validated['id_wilayah']);
        $pelayanan = Pelayanan::findOrFail($validated['id_pelayanan']);

        return response()->json([
            'nama_ibadah' => KehadiranIbadah::buildNamaIbadah($wilayah, $pelayanan),
        ]);
    }

    private function validatedPayload(Request $request): array
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'id_wilayah');

        $manualName = $request->boolean('is_nama_manual');
        $nameRule = $manualName ? 'required' : 'nullable';

        $validated = $request->validate([
            'id_wilayah' => 'required|exists:wilayahs,id',
            'id_pelayanan' => 'required|exists:pelayanans,id',
            'nama_ibadah' => [$nameRule, 'string', 'max:255'],
            'is_nama_manual' => 'nullable|boolean',
            'tanggal_ibadah' => 'required|date|before_or_equal:' . Carbon::today()->toDateString(),
            'hadir_pria_onsite' => 'required|integer|min:0',
            'hadir_wanita_onsite' => 'required|integer|min:0',
            'hadir_pria_online' => 'required|integer|min:0',
            'hadir_wanita_online' => 'required|integer|min:0',
            'baru_pria' => 'required|integer|min:0',
            'baru_wanita' => 'required|integer|min:0',
        ], [
            'tanggal_ibadah.before_or_equal' => 'Tanggal ibadah tidak boleh lebih dari hari ini.',
            'nama_ibadah.required' => 'Nama ibadah wajib diisi untuk ibadah lainnya.',
        ]);

        $wilayah = Wilayah::findOrFail($validated['id_wilayah']);
        $pelayanan = Pelayanan::findOrFail($validated['id_pelayanan']);

        $data = [
            'id_wilayah' => $validated['id_wilayah'],
            'id_pelayanan' => $validated['id_pelayanan'],
            'is_nama_manual' => $manualName,
            'nama_ibadah' => $manualName
                ? trim((string) $validated['nama_ibadah'])
                : KehadiranIbadah::buildNamaIbadah($wilayah, $pelayanan),
            'tanggal_ibadah' => $validated['tanggal_ibadah'],
            'hadir_pria_onsite' => (int) $validated['hadir_pria_onsite'],
            'hadir_wanita_onsite' => (int) $validated['hadir_wanita_onsite'],
            'hadir_pria_online' => (int) $validated['hadir_pria_online'],
            'hadir_wanita_online' => (int) $validated['hadir_wanita_online'],
            'baru_pria' => (int) $validated['baru_pria'],
            'baru_wanita' => (int) $validated['baru_wanita'],
        ];

        $data['total_hadir_onsite'] = $data['hadir_pria_onsite'] + $data['hadir_wanita_onsite'];
        $data['total_hadir_online'] = $data['hadir_pria_online'] + $data['hadir_wanita_online'];
        $data['total_baru'] = $data['baru_pria'] + $data['baru_wanita'];
        $data['grand_total'] = $data['total_hadir_onsite'] + $data['total_hadir_online'] + $data['total_baru'];

        return $data;
    }

    private function buildIndexQuery(Request $request)
    {
        $query = KehadiranIbadah::with(['wilayah', 'pelayanan']);

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_ibadah', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_ibadah', '<=', $request->date_to);
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
                $subQuery->where('nama_ibadah', 'like', "%{$search}%")
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"))
                    ->orWhere('grand_total', 'like', "%{$search}%");
            });
        }

        return $query;
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
