<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LaporanPa;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\AnakBimbingan;
use App\Models\MasterBukuPa;
use App\Services\Audit\AuditLogger;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LaporanPaController extends Controller
{
    /**
     * Expression for grouping dates by year-month across supported databases.
     */
    private function yearMonthExpression(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }

    /**
     * Expression for the unique activity key: anak PA + active month.
     */
    private function anakPaMonthExpression(): string
    {
        $bulanExpression = $this->yearMonthExpression('laporan_pas.tanggal_pa');

        return DB::connection()->getDriverName() === 'sqlite'
            ? "laporan_pas.anak_pa_id || '-' || {$bulanExpression}"
            : "CONCAT(laporan_pas.anak_pa_id, '-', {$bulanExpression})";
    }

    // Menampilkan daftar laporan PA
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $laporanPas = $this->buildIndexQuery($request)
            ->latest('tanggal_pa')
            ->get();

        return view('laporan_pa.index', compact('laporanPas', 'wilayahs', 'pelayanans'));
    }

    // Form untuk menambah laporan PA baru
    public function create(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $bukuPas = MasterBukuPa::approved()->orderBy('nama_buku')->get();

        return view('laporan_pa.create', compact('wilayahs', 'pelayanans', 'bukuPas'));
    }

    /**
     * Ticket 1 & 2: Menyimpan laporan PA dengan batch insert.
     * - Ticket 1: buku_pa_id nullable, validasi buku_pa_lainnya conditional.
     * - Ticket 2: anak_pa_ids[] array, loop insert per anak PA.
     */
    public function store(Request $request)
    {
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

        // Sanitasi: ubah 'lainnya' menjadi null sebelum validasi
        if ($request->buku_pa_id === 'lainnya') {
            $request->merge(['buku_pa_id' => null]);
        }

        $rules = [
            'wilayah_id'    => 'required|exists:wilayahs,id',
            'pelayanan_id'  => 'required|exists:pelayanans,id',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'anak_pa_ids'   => 'required|array|min:1',               // Ticket 2: array
            'anak_pa_ids.*' => 'required|distinct|exists:anak_bimbingans,id', // Ticket 2: tiap elemen valid
            'buku_pa_id'    => 'nullable|exists:master_buku_pas,id', // Ticket 1: nullable
            'bab'           => 'required|integer|min:1',
            'tanggal_pa'    => 'required|date|before_or_equal:' . Carbon::today()->toDateString(),
        ];

        // Ticket 1: Jika buku_pa_id null → buku_pa_lainnya wajib diisi
        if (empty($request->buku_pa_id)) {
            $rules['buku_pa_lainnya'] = 'required|string|max:255';
        }

        $request->validate($rules, [
            'tanggal_pa.before_or_equal' => 'Tanggal PA tidak boleh lebih dari hari ini.',
            'buku_pa_lainnya.required'   => 'Nama buku PA wajib diisi jika memilih "Lainnya".',
            'anak_pa_ids.required'       => 'Pilih minimal 1 anak PA.',
            'anak_pa_ids.min'            => 'Pilih minimal 1 anak PA.',
        ]);

        $this->validatePaRelations($request, $request->anak_pa_ids);

        // Siapkan data shared (sama untuk semua anak PA)
        $sharedData = [
            'wilayah_id'    => $request->wilayah_id,
            'pelayanan_id'  => $request->pelayanan_id,
            'pembimbing_id' => $request->pembimbing_id,
            'buku_pa_id'    => $request->buku_pa_id,
            'buku_pa_lainnya' => $request->buku_pa_id ? null : $request->buku_pa_lainnya,
            'bab'           => $request->bab,
            'tanggal_pa'    => $request->tanggal_pa,
        ];

        // Ticket 2: Batch insert — 1 row per anak PA
        $anakPaIds = $request->anak_pa_ids;
        $rows = [];
        $now = now();

        foreach ($anakPaIds as $anakId) {
            $rows[] = array_merge($sharedData, [
                'anak_pa_id'  => $anakId,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        LaporanPa::insert($rows);

        $count = count($anakPaIds);
        app(AuditLogger::class)->log(AuditLog::EVENT_CREATED, [
            'module' => 'pa',
            'auditable_type' => LaporanPa::class,
            'auditable_label' => "Batch Laporan PA ({$count} anak)",
            'new_values' => $sharedData,
            'metadata' => [
                'created_count' => $count,
                'anak_pa_ids' => array_values($anakPaIds),
            ],
        ]);

        return redirect()->route('laporan_pa.index')
            ->with('success', "Laporan PA berhasil disimpan untuk {$count} anak PA.");
    }

    // Form untuk mengedit laporan PA
    public function edit(Request $request, LaporanPa $laporanPa)
    {
        $this->abortIfOutsideRegion($request, $laporanPa->wilayah_id);

        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $bukuPas = MasterBukuPa::approved()->orderBy('nama_buku')->get();

        // Pre-load pembimbing & anak PA untuk form edit
        $pembimbings = Pembimbing::where('wilayah_id', $laporanPa->wilayah_id)
            ->where('pelayanan_id', $laporanPa->pelayanan_id)
            ->get();

        $anakPas = AnakBimbingan::where('pembimbing_id', $laporanPa->pembimbing_id)->get();

        return view('laporan_pa.edit', compact(
            'laporanPa', 'wilayahs', 'pelayanans', 'bukuPas', 'pembimbings', 'anakPas'
        ));
    }

    /**
     * Ticket 1: Mengupdate data laporan PA — buku_pa_id nullable fix.
     */
    public function update(Request $request, LaporanPa $laporanPa)
    {
        $this->abortIfOutsideRegion($request, $laporanPa->wilayah_id);
        $this->dataScope()->injectRegionIntoRequest($request, 'wilayah_id');

        // Sanitasi: ubah 'lainnya' menjadi null
        if ($request->buku_pa_id === 'lainnya') {
            $request->merge(['buku_pa_id' => null]);
        }

        $rules = [
            'wilayah_id'    => 'required|exists:wilayahs,id',
            'pelayanan_id'  => 'required|exists:pelayanans,id',
            'pembimbing_id' => 'required|exists:pembimbings,id',
            'anak_pa_id'    => 'required|exists:anak_bimbingans,id',
            'buku_pa_id'    => 'nullable|exists:master_buku_pas,id',
            'bab'           => 'required|integer|min:1',
            'tanggal_pa'    => 'required|date|before_or_equal:' . Carbon::today()->toDateString(),
        ];

        if (empty($request->buku_pa_id)) {
            $rules['buku_pa_lainnya'] = 'required|string|max:255';
        }

        $request->validate($rules, [
            'tanggal_pa.before_or_equal' => 'Tanggal PA tidak boleh lebih dari hari ini.',
            'buku_pa_lainnya.required'   => 'Nama buku PA wajib diisi jika memilih "Lainnya".',
        ]);

        $this->validatePaRelations($request, [$request->anak_pa_id]);

        $data = $request->only([
            'wilayah_id', 'pelayanan_id', 'pembimbing_id', 'anak_pa_id', 'bab', 'tanggal_pa'
        ]);

        $data['buku_pa_id'] = $request->buku_pa_id;
        $data['buku_pa_lainnya'] = $request->buku_pa_id ? null : $request->buku_pa_lainnya;

        $laporanPa->update($data);

        return redirect()->route('laporan_pa.index')
            ->with('success', 'Laporan PA berhasil diperbarui.');
    }

    /**
     * Ticket 4: Soft Delete laporan PA via AJAX.
     */
    public function destroy(LaporanPa $laporanPa)
    {
        $this->abortIfOutsideRegion(request(), $laporanPa->wilayah_id);

        $laporanPa->delete(); // Soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Laporan PA berhasil dihapus.']);
        }

        return redirect()->route('laporan_pa.index')
            ->with('success', 'Laporan PA berhasil dihapus.');
    }

    // =========================================
    // API Endpoints untuk Cascading Dropdowns
    // =========================================

    /**
     * API: Mendapatkan pembimbing berdasarkan wilayah & pelayanan.
     */
    public function getPembimbing(Request $request)
    {
        $query = Pembimbing::query();

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('pelayanan_id')) {
            $query->where('pelayanan_id', $request->pelayanan_id);
        }

        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');

        $pembimbings = $query->orderBy('nama_pembimbing')->get(['id', 'nama_pembimbing']);

        return response()->json($pembimbings);
    }

    /**
     * API: Mendapatkan anak PA berdasarkan pembimbing.
     */
    public function getAnakPa(Request $request)
    {
        $query = AnakBimbingan::query();

        if ($request->filled('pembimbing_id')) {
            $query->where('pembimbing_id', $request->pembimbing_id);
        }

        $this->dataScope()->applyToRequestQuery($query, $request, 'wilayah_id');

        $anakPas = $query->orderBy('nama_anak')->get(['id', 'nama_anak']);

        return response()->json($anakPas);
    }

    // =========================================
    // Ticket 3: Report Keaktifan PA + Export CSV
    // =========================================

    /**
     * Menampilkan halaman report Keaktifan PA.
     */
    public function report(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $reportData = null;

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $reportData = $this->buildReportQuery($request);
        }

        return view('laporan_pa.report', compact('wilayahs', 'pelayanans', 'reportData'));
    }

    /**
     * Export data Keaktifan PA ke CSV.
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $reportData = $this->buildReportQuery($request);

        $filename = 'laporan_keaktifan_pa_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reportData, $request) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header info
            fputcsv($file, ['Laporan Keaktifan PA - GKKD Jakarta']);
            fputcsv($file, ['Periode: ' . $request->date_from . ' s/d ' . $request->date_to]);
            fputcsv($file, []);

            // Column headers
            fputcsv($file, ['Wilayah', 'Pelayanan', 'Nama Buku PA', 'Jumlah Anak PA Aktif']);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row->nama_wilayah,
                    $row->nama_pelayanan,
                    $row->nama_buku_display,
                    $row->jumlah_anak_aktif,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export daftar Laporan PA ke CSV atau Excel.
     */
    public function exportIndex(Request $request)
    {
        $laporanPas = $this->buildIndexQuery($request)
            ->latest('tanggal_pa')
            ->get();

        return SimpleTableExporter::download(
            'laporan_pa',
            ['Tanggal', 'Anak PA', 'Pembimbing', 'Wilayah', 'Pelayanan', 'Buku', 'Bab'],
            $laporanPas,
            fn (LaporanPa $laporan) => [
                optional($laporan->tanggal_pa)->format('Y-m-d'),
                $laporan->anakPa->nama_anak ?? '-',
                $laporan->pembimbing->nama_pembimbing ?? '-',
                $laporan->wilayah->nama_wilayah ?? '-',
                $laporan->pelayanan->nama_pelayanan ?? '-',
                $laporan->nama_buku,
                $laporan->bab,
            ],
            $request->get('format', 'csv')
        );
    }

    /**
     * Export report Keaktifan PA ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $reportData = $this->buildReportQuery($request);

        return SimpleTableExporter::download(
            'laporan_keaktifan_pa',
            ['Wilayah', 'Pelayanan', 'Nama Buku PA', 'Jumlah Anak PA Aktif'],
            $reportData,
            fn ($row) => [
                $row->nama_wilayah,
                $row->nama_pelayanan,
                $row->nama_buku_display,
                $row->jumlah_anak_aktif,
            ],
            'excel'
        );
    }

    /**
     * Query builder untuk daftar Laporan PA.
     */
    private function buildIndexQuery(Request $request)
    {
        $query = LaporanPa::with(['wilayah', 'pelayanan', 'pembimbing', 'anakPa', 'bukuPa']);

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pa', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pa', '<=', $request->date_to);
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
                $subQuery->where('buku_pa_lainnya', 'like', "%{$search}%")
                    ->orWhere('bab', 'like', "%{$search}%")
                    ->orWhereHas('anakPa', fn ($relation) => $relation->where('nama_anak', 'like', "%{$search}%"))
                    ->orWhereHas('pembimbing', fn ($relation) => $relation->where('nama_pembimbing', 'like', "%{$search}%"))
                    ->orWhereHas('bukuPa', fn ($relation) => $relation->where('nama_buku', 'like', "%{$search}%"))
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    /**
     * Query builder untuk report Keaktifan PA.
     *
     * Business Rule: Dalam 1 bulan, 1 Pembimbing yang melakukan PA
     * dengan 1 Anak PA yang sama hanya dihitung 1 Keaktifan,
     * walaupun mereka PA lebih dari 2 kali di bulan tersebut.
     *
     * Menggunakan subquery distinct count pada kombinasi
     * anak_pa_id + bulan tanggal_pa.
     */
    private function buildReportQuery(Request $request)
    {
        $anakPaMonthExpression = $this->anakPaMonthExpression();

        $query = DB::table('laporan_pas')
            ->join('wilayahs', 'laporan_pas.wilayah_id', '=', 'wilayahs.id')
            ->join('pelayanans', 'laporan_pas.pelayanan_id', '=', 'pelayanans.id')
            ->leftJoin('master_buku_pas', 'laporan_pas.buku_pa_id', '=', 'master_buku_pas.id')
            ->whereNull('laporan_pas.deleted_at')
            ->whereBetween('laporan_pas.tanggal_pa', [$request->date_from, $request->date_to]);

        // Filter optional
        if ($request->filled('wilayah_id')) {
            $query->where('laporan_pas.wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('pelayanan_id')) {
            $query->where('laporan_pas.pelayanan_id', $request->pelayanan_id);
        }

        $this->dataScope()->applyToRequestQuery($query, $request, 'laporan_pas.wilayah_id');

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('wilayahs.nama_wilayah', 'like', "%{$search}%")
                    ->orWhere('pelayanans.nama_pelayanan', 'like', "%{$search}%")
                    ->orWhere('master_buku_pas.nama_buku', 'like', "%{$search}%")
                    ->orWhere('laporan_pas.buku_pa_lainnya', 'like', "%{$search}%");
            });
        }

        // Distinct count: anak_pa_id + MONTH(tanggal_pa) dianggap 1 keaktifan
        // Kita hitung jumlah pasangan unik (anak_pa_id, bulan) per grup
        $results = $query->select(
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan',
                DB::raw("COALESCE(master_buku_pas.nama_buku, laporan_pas.buku_pa_lainnya, '-') as nama_buku_display"),
                DB::raw("COUNT(DISTINCT {$anakPaMonthExpression}) as jumlah_anak_aktif")
            )
            ->groupBy(
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan',
                DB::raw("COALESCE(master_buku_pas.nama_buku, laporan_pas.buku_pa_lainnya, '-')")
            )
            ->orderBy('wilayahs.nama_wilayah')
            ->orderBy('pelayanans.nama_pelayanan')
            ->get();

        return $results;
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

    private function validatePaRelations(Request $request, array $anakPaIds): void
    {
        $pembimbing = Pembimbing::find($request->pembimbing_id);

        if (! $pembimbing
            || (int) $pembimbing->wilayah_id !== (int) $request->wilayah_id
            || (int) $pembimbing->pelayanan_id !== (int) $request->pelayanan_id) {
            throw ValidationException::withMessages([
                'pembimbing_id' => 'Pembimbing tidak sesuai dengan wilayah dan pelayanan.',
            ]);
        }

        $validAnakCount = AnakBimbingan::whereIn('id', $anakPaIds)
            ->where('pembimbing_id', $request->pembimbing_id)
            ->where('wilayah_id', $request->wilayah_id)
            ->where('pelayanan_id', $request->pelayanan_id)
            ->count();

        if ($validAnakCount !== count($anakPaIds)) {
            throw ValidationException::withMessages([
                'anak_pa_id' => 'Ada anak PA yang tidak sesuai dengan pembimbing, wilayah, atau pelayanan.',
            ]);
        }
    }
}
