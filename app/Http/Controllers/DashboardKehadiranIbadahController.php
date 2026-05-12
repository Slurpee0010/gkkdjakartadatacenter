<?php

namespace App\Http\Controllers;

use App\Models\KehadiranIbadah;
use App\Models\Pelayanan;
use App\Models\Wilayah;
use App\Services\Rbac\DataScope;
use App\Support\Exports\SimpleTableExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardKehadiranIbadahController extends Controller
{
    public function index(Request $request)
    {
        $wilayahs = $this->dataScope()->wilayahOptionsFor($request->user());
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $periode = $this->selectedPeriod($request);
        $filterWilayah = $this->dataScope()->scopedWilayahIdForRequest($request, 'id_wilayah');
        $filterPelayanan = $request->get('id_pelayanan');

        $reportA = $this->buildSummaryPerMinggu($request);
        $reportB = $this->buildAverageAttendance($periode, $filterWilayah, $filterPelayanan);
        $filteredRows = $this->buildBaseQuery($request)->get();

        $stats = [
            'jumlah_ibadah' => $filteredRows->count(),
            'grand_total' => $filteredRows->sum('grand_total'),
            'total_onsite' => $filteredRows->sum('total_hadir_onsite'),
            'total_online' => $filteredRows->sum('total_hadir_online'),
            'total_baru' => $filteredRows->sum('total_baru'),
            'avg_grand_total' => $filteredRows->count() > 0
                ? round($filteredRows->avg('grand_total'), 1)
                : 0,
        ];

        $maxReportAGrand = max(1, (int) ($reportA->max('grand_total') ?? 0));
        $maxReportBAvg = max(1, (float) ($reportB->max('avg_grand_total') ?? 0));

        return view('dashboard_kehadiran_ibadah.index', compact(
            'wilayahs',
            'pelayanans',
            'periode',
            'filterWilayah',
            'filterPelayanan',
            'reportA',
            'reportB',
            'stats',
            'maxReportAGrand',
            'maxReportBAvg'
        ));
    }

    public function export(Request $request)
    {
        $reportType = strtoupper((string) $request->get('report', 'A'));
        $periode = $this->selectedPeriod($request);
        $filterWilayah = $this->dataScope()->scopedWilayahIdForRequest($request, 'id_wilayah');
        $filterPelayanan = $request->get('id_pelayanan');

        if ($reportType === 'B') {
            $rows = $this->buildAverageAttendance($periode, $filterWilayah, $filterPelayanan);

            return SimpleTableExporter::download(
                'dashboard_kehadiran_ibadah_rata_rata',
                [
                    'Wilayah',
                    'Pelayanan',
                    'Jumlah Ibadah',
                    'Rata-rata Onsite',
                    'Rata-rata Online',
                    'Rata-rata Baru',
                    'Rata-rata Grand Total',
                    'Total Grand Total',
                    'Tanggal Terakhir',
                ],
                $rows,
                fn ($row) => [
                    $row->nama_wilayah,
                    $row->nama_pelayanan,
                    $row->jumlah_ibadah,
                    $row->avg_onsite,
                    $row->avg_online,
                    $row->avg_baru,
                    $row->avg_grand_total,
                    $row->sum_grand_total,
                    $row->tanggal_terakhir,
                ],
                $request->get('format', 'csv')
            );
        }

        $rows = $this->buildSummaryPerMinggu($request);

        return SimpleTableExporter::download(
            'dashboard_kehadiran_ibadah_mingguan',
            [
                'Minggu Mulai',
                'Minggu Selesai',
                'Wilayah',
                'Pelayanan',
                'Jumlah Ibadah',
                'Total Onsite',
                'Total Online',
                'Total Baru',
                'Grand Total',
            ],
            $rows,
            fn ($row) => [
                $row->minggu_mulai,
                $row->minggu_selesai,
                $row->nama_wilayah,
                $row->nama_pelayanan,
                $row->jumlah_ibadah,
                $row->total_onsite,
                $row->total_online,
                $row->total_baru,
                $row->grand_total,
            ],
            $request->get('format', 'csv')
        );
    }

    public function summaryWeeklyApi(Request $request)
    {
        return response()->json([
            'data' => $this->buildSummaryPerMinggu($request)->values(),
        ]);
    }

    public function averageAttendanceApi(Request $request)
    {
        $periode = $this->selectedPeriod($request);

        return response()->json([
            'periode' => $periode,
            'data' => $this->buildAverageAttendance(
                $periode,
                $this->dataScope()->scopedWilayahIdForRequest($request, 'id_wilayah'),
                $request->get('id_pelayanan')
            ),
        ]);
    }

    private function buildSummaryPerMinggu(Request $request)
    {
        $rows = $this->buildBaseQuery($request)
            ->oldest('tanggal_ibadah')
            ->get();

        $summary = $rows->groupBy(function (KehadiranIbadah $item) {
            $weekStart = $item->tanggal_ibadah->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

            return $weekStart . '|' . $item->id_wilayah . '|' . $item->id_pelayanan;
        })->map(function ($group, string $key) {
            [$weekStartText] = explode('|', $key);
            $weekStart = Carbon::parse($weekStartText);
            $first = $group->first();

            return (object) [
                'minggu_mulai' => $weekStart->format('Y-m-d'),
                'minggu_selesai' => $weekStart->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'),
                'minggu_label' => $weekStart->format('d M') . ' - ' . $weekStart->copy()->endOfWeek(Carbon::SUNDAY)->format('d M Y'),
                'id_wilayah' => $first->id_wilayah,
                'id_pelayanan' => $first->id_pelayanan,
                'nama_wilayah' => $first->wilayah->nama_wilayah ?? '-',
                'nama_pelayanan' => $first->pelayanan->nama_pelayanan ?? '-',
                'jumlah_ibadah' => $group->count(),
                'total_onsite' => $group->sum('total_hadir_onsite'),
                'total_online' => $group->sum('total_hadir_online'),
                'total_baru' => $group->sum('total_baru'),
                'grand_total' => $group->sum('grand_total'),
            ];
        })->values();

        return $summary->sort(function ($a, $b) {
            $dateCompare = strcmp($b->minggu_mulai, $a->minggu_mulai);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            $wilayahCompare = strcmp($a->nama_wilayah, $b->nama_wilayah);
            if ($wilayahCompare !== 0) {
                return $wilayahCompare;
            }

            return strcmp($a->nama_pelayanan, $b->nama_pelayanan);
        })->values();
    }

    private function buildAverageAttendance(int $periode, $wilayahId, $pelayananId)
    {
        $dateFrom = Carbon::today()->subMonthsNoOverflow($periode)->toDateString();
        $dateTo = Carbon::today()->toDateString();

        return DB::table('kehadiran_ibadah')
            ->join('wilayahs', 'kehadiran_ibadah.id_wilayah', '=', 'wilayahs.id')
            ->join('pelayanans', 'kehadiran_ibadah.id_pelayanan', '=', 'pelayanans.id')
            ->whereNull('kehadiran_ibadah.deleted_at')
            ->whereDate('kehadiran_ibadah.tanggal_ibadah', '>=', $dateFrom)
            ->whereDate('kehadiran_ibadah.tanggal_ibadah', '<=', $dateTo)
            ->when($wilayahId, fn ($query) => $query->where('kehadiran_ibadah.id_wilayah', $wilayahId))
            ->when($pelayananId, fn ($query) => $query->where('kehadiran_ibadah.id_pelayanan', $pelayananId))
            ->select(
                'kehadiran_ibadah.id_wilayah',
                'kehadiran_ibadah.id_pelayanan',
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan',
                DB::raw('COUNT(*) as jumlah_ibadah'),
                DB::raw('ROUND(AVG(kehadiran_ibadah.total_hadir_onsite), 1) as avg_onsite'),
                DB::raw('ROUND(AVG(kehadiran_ibadah.total_hadir_online), 1) as avg_online'),
                DB::raw('ROUND(AVG(kehadiran_ibadah.total_baru), 1) as avg_baru'),
                DB::raw('ROUND(AVG(kehadiran_ibadah.grand_total), 1) as avg_grand_total'),
                DB::raw('SUM(kehadiran_ibadah.grand_total) as sum_grand_total'),
                DB::raw('MAX(kehadiran_ibadah.tanggal_ibadah) as tanggal_terakhir')
            )
            ->groupBy(
                'kehadiran_ibadah.id_wilayah',
                'kehadiran_ibadah.id_pelayanan',
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan'
            )
            ->orderByDesc('avg_grand_total')
            ->orderBy('wilayahs.nama_wilayah')
            ->orderBy('pelayanans.nama_pelayanan')
            ->get();
    }

    private function buildBaseQuery(Request $request)
    {
        $query = KehadiranIbadah::with(['wilayah', 'pelayanan'])
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('tanggal_ibadah', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('tanggal_ibadah', '<=', $request->date_to))
            ->when($request->filled('id_wilayah'), fn ($query) => $query->where('id_wilayah', $request->id_wilayah))
            ->when($request->filled('id_pelayanan'), fn ($query) => $query->where('id_pelayanan', $request->id_pelayanan));

        $this->dataScope()->applyToRequestQuery($query, $request, 'id_wilayah');

        return $query;
    }

    private function selectedPeriod(Request $request): int
    {
        $periode = (int) $request->get('periode', 3);

        return in_array($periode, [1, 3, 6, 12], true) ? $periode : 3;
    }

    private function dataScope(): DataScope
    {
        return app(DataScope::class);
    }
}
