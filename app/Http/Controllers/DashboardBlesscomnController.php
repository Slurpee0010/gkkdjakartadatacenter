<?php

namespace App\Http\Controllers;

use App\Models\MasterBlesscomn;
use App\Models\LaporanBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardBlesscomnController extends Controller
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
     * Menampilkan halaman dashboard analytics Blesscomn.
     */
    public function index(Request $request)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();

        // Periode filter
        $periode = $request->get('periode', '3'); // default 3 bulan
        $dateFrom = Carbon::now()->subMonths((int) $periode)->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();

        // Global filters
        $filterWilayah = $request->get('id_wilayah');
        $filterPelayanan = $request->get('id_pelayanan');

        // ============================
        // Report A: Populasi Blesscomn
        // ============================
        $reportA = $this->buildReportPopulasi($filterWilayah, $filterPelayanan);

        // ============================
        // Report B: Rata-rata Keaktifan
        // ============================
        $reportB = $this->buildReportKeaktifan($dateFrom, $dateTo, $filterWilayah, $filterPelayanan, (int) $periode);

        // ============================
        // Report C: Top Leaderboard
        // ============================
        $reportC = $this->buildReportLeaderboard($dateFrom, $dateTo, $filterWilayah, $filterPelayanan);

        // ============================
        // Report D: Streaks (Ter-Aktif)
        // ============================
        $reportD = $this->buildReportStreaks($filterWilayah, $filterPelayanan);

        return view('dashboard_blesscomn.index', compact(
            'wilayahs', 'pelayanans', 'periode',
            'reportA', 'reportB', 'reportC', 'reportD',
            'filterWilayah', 'filterPelayanan',
            'dateFrom', 'dateTo'
        ));
    }

    /**
     * Report A: Count total master_blesscomn, filterable by Wilayah/Pelayanan.
     */
    private function buildReportPopulasi($wilayahId, $pelayananId)
    {
        $query = MasterBlesscomn::query();

        if ($wilayahId) {
            $query->where('id_wilayah', $wilayahId);
        }
        if ($pelayananId) {
            $query->where('id_pelayanan', $pelayananId);
        }

        $total = $query->count();

        // Breakdown per wilayah
        $perWilayah = MasterBlesscomn::select('id_wilayah', DB::raw('COUNT(*) as jumlah'))
            ->when($wilayahId, fn($q) => $q->where('id_wilayah', $wilayahId))
            ->when($pelayananId, fn($q) => $q->where('id_pelayanan', $pelayananId))
            ->groupBy('id_wilayah')
            ->with('wilayah')
            ->get();

        return [
            'total' => $total,
            'per_wilayah' => $perWilayah,
        ];
    }

    /**
     * Report B: Rata-rata Keaktifan.
     * COUNT DISTINCT bulan per id_blesscomn, walaupun ada 4 laporan dalam 1 bulan hitung 1.
     */
    private function buildReportKeaktifan($dateFrom, $dateTo, $wilayahId, $pelayananId, $totalBulan)
    {
        $bulanExpression = $this->yearMonthExpression('laporan_blesscomns.tanggal_pelaksanaan');

        $query = DB::table('laporan_blesscomns')
            ->join('master_blesscomns', 'laporan_blesscomns.id_blesscomn', '=', 'master_blesscomns.id')
            ->join('wilayahs', 'laporan_blesscomns.id_wilayah', '=', 'wilayahs.id')
            ->join('pelayanans', 'laporan_blesscomns.id_pelayanan', '=', 'pelayanans.id')
            ->whereNull('laporan_blesscomns.deleted_at')
            ->whereBetween('laporan_blesscomns.tanggal_pelaksanaan', [$dateFrom, $dateTo]);

        if ($wilayahId) {
            $query->where('laporan_blesscomns.id_wilayah', $wilayahId);
        }
        if ($pelayananId) {
            $query->where('laporan_blesscomns.id_pelayanan', $pelayananId);
        }

        $results = $query->select(
            'laporan_blesscomns.id_blesscomn',
            'master_blesscomns.nama_blesscomn',
            'wilayahs.nama_wilayah',
            'pelayanans.nama_pelayanan',
            DB::raw("COUNT(DISTINCT {$bulanExpression}) as bulan_aktif")
        )
            ->groupBy(
                'laporan_blesscomns.id_blesscomn',
                'master_blesscomns.nama_blesscomn',
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan'
            )
            ->orderByDesc('bulan_aktif')
            ->get();

        // Hitung persentase keaktifan per blesscomn
        $results->transform(function ($item) use ($totalBulan) {
            $item->persentase = $totalBulan > 0
                ? round(($item->bulan_aktif / $totalBulan) * 100, 1)
                : 0;
            return $item;
        });

        return $results;
    }

    /**
     * Report C: Top Leaderboard.
     * Diurutkan berdasarkan rata-rata total_hadir (DESC) dan sum total_baru (DESC).
     */
    private function buildReportLeaderboard($dateFrom, $dateTo, $wilayahId, $pelayananId)
    {
        $query = DB::table('laporan_blesscomns')
            ->join('master_blesscomns', 'laporan_blesscomns.id_blesscomn', '=', 'master_blesscomns.id')
            ->join('wilayahs', 'laporan_blesscomns.id_wilayah', '=', 'wilayahs.id')
            ->join('pelayanans', 'laporan_blesscomns.id_pelayanan', '=', 'pelayanans.id')
            ->whereNull('laporan_blesscomns.deleted_at')
            ->whereBetween('laporan_blesscomns.tanggal_pelaksanaan', [$dateFrom, $dateTo]);

        if ($wilayahId) {
            $query->where('laporan_blesscomns.id_wilayah', $wilayahId);
        }
        if ($pelayananId) {
            $query->where('laporan_blesscomns.id_pelayanan', $pelayananId);
        }

        return $query->select(
            'laporan_blesscomns.id_blesscomn',
            'master_blesscomns.nama_blesscomn',
            'wilayahs.nama_wilayah',
            'pelayanans.nama_pelayanan',
            DB::raw('ROUND(AVG(laporan_blesscomns.total_hadir), 1) as avg_hadir'),
            DB::raw('SUM(laporan_blesscomns.total_baru) as sum_baru'),
            DB::raw('COUNT(*) as jumlah_laporan')
        )
            ->groupBy(
                'laporan_blesscomns.id_blesscomn',
                'master_blesscomns.nama_blesscomn',
                'wilayahs.nama_wilayah',
                'pelayanans.nama_pelayanan'
            )
            ->orderByDesc('avg_hadir')
            ->orderByDesc('sum_baru')
            ->limit(20)
            ->get();
    }

    /**
     * Report D: Blesscomn Ter-Aktif / Streaks.
     * Cari Blesscomn yang selalu ada minimal 1 laporan setiap bulan berturut-turut.
     */
    private function buildReportStreaks($wilayahId, $pelayananId)
    {
        $bulanExpression = $this->yearMonthExpression('tanggal_pelaksanaan');

        // Ambil semua blesscomn dengan laporan
        $blesscomnQuery = MasterBlesscomn::with(['wilayah', 'pelayanan'])
            ->when($wilayahId, fn($q) => $q->where('id_wilayah', $wilayahId))
            ->when($pelayananId, fn($q) => $q->where('id_pelayanan', $pelayananId))
            ->has('laporans')
            ->get();

        $results = collect();
        $now = Carbon::now();

        foreach ($blesscomnQuery as $blesscomn) {
            // Ambil semua bulan unik dari laporan blesscomn ini
            $bulanAktif = LaporanBlesscomn::where('id_blesscomn', $blesscomn->id)
                ->select(DB::raw("DISTINCT {$bulanExpression} as bulan"))
                ->orderByDesc('bulan')
                ->pluck('bulan')
                ->toArray();

            if (empty($bulanAktif)) continue;

            // Hitung streak dari bulan terbaru ke belakang
            $streak = 0;
            $currentMonth = $now->copy()->startOfMonth();

            // Cek apakah bulan ini ada laporannya
            $currentMonthStr = $currentMonth->format('Y-m');
            if (!in_array($currentMonthStr, $bulanAktif)) {
                // Mulai dari bulan lalu
                $currentMonth->subMonth();
            }

            while (in_array($currentMonth->format('Y-m'), $bulanAktif)) {
                $streak++;
                $currentMonth->subMonth();
            }

            if ($streak >= 2) {
                $results->push((object) [
                    'nama_blesscomn' => $blesscomn->nama_blesscomn,
                    'nama_wilayah' => $blesscomn->wilayah->nama_wilayah ?? '-',
                    'nama_pelayanan' => $blesscomn->pelayanan->nama_pelayanan ?? '-',
                    'streak' => $streak,
                    'bulan_terakhir' => $bulanAktif[0] ?? '-',
                ]);
            }
        }

        return $results->sortByDesc('streak')->values();
    }

    /**
     * Export Report ke CSV.
     */
    public function exportCsv(Request $request)
    {
        $reportType = $request->get('report', 'A');
        $periode = (int) $request->get('periode', '3');
        $dateFrom = Carbon::now()->subMonths($periode)->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();
        $filterWilayah = $request->get('id_wilayah');
        $filterPelayanan = $request->get('id_pelayanan');

        $filename = 'blesscomn_report_' . strtolower($reportType) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reportType, $dateFrom, $dateTo, $filterWilayah, $filterPelayanan, $periode) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            switch ($reportType) {
                case 'A':
                    $data = $this->buildReportPopulasi($filterWilayah, $filterPelayanan);
                    fputcsv($file, ['Report Populasi Blesscomn - GKKD Jakarta']);
                    fputcsv($file, ['Total Blesscomn: ' . $data['total']]);
                    fputcsv($file, []);
                    fputcsv($file, ['Wilayah', 'Jumlah Blesscomn']);
                    foreach ($data['per_wilayah'] as $row) {
                        fputcsv($file, [
                            $row->wilayah->nama_wilayah ?? '-',
                            $row->jumlah,
                        ]);
                    }
                    break;

                case 'B':
                    $data = $this->buildReportKeaktifan($dateFrom, $dateTo, $filterWilayah, $filterPelayanan, $periode);
                    fputcsv($file, ['Report Keaktifan Blesscomn - GKKD Jakarta']);
                    fputcsv($file, ['Periode: ' . $periode . ' bulan']);
                    fputcsv($file, []);
                    fputcsv($file, ['Nama Blesscomn', 'Wilayah', 'Pelayanan', 'Bulan Aktif', 'Persentase (%)']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->nama_blesscomn,
                            $row->nama_wilayah,
                            $row->nama_pelayanan,
                            $row->bulan_aktif,
                            $row->persentase . '%',
                        ]);
                    }
                    break;

                case 'C':
                    $data = $this->buildReportLeaderboard($dateFrom, $dateTo, $filterWilayah, $filterPelayanan);
                    fputcsv($file, ['Report Leaderboard Blesscomn - GKKD Jakarta']);
                    fputcsv($file, ['Periode: ' . $periode . ' bulan']);
                    fputcsv($file, []);
                    fputcsv($file, ['Rank', 'Nama Blesscomn', 'Wilayah', 'Pelayanan', 'Rata-rata Hadir', 'Total Baru', 'Jumlah Laporan']);
                    foreach ($data as $i => $row) {
                        fputcsv($file, [
                            $i + 1,
                            $row->nama_blesscomn,
                            $row->nama_wilayah,
                            $row->nama_pelayanan,
                            $row->avg_hadir,
                            $row->sum_baru,
                            $row->jumlah_laporan,
                        ]);
                    }
                    break;

                case 'D':
                    $data = $this->buildReportStreaks($filterWilayah, $filterPelayanan);
                    fputcsv($file, ['Report Streaks Blesscomn - GKKD Jakarta']);
                    fputcsv($file, []);
                    fputcsv($file, ['Nama Blesscomn', 'Wilayah', 'Pelayanan', 'Streak (Bulan)', 'Bulan Terakhir']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->nama_blesscomn,
                            $row->nama_wilayah,
                            $row->nama_pelayanan,
                            $row->streak,
                            $row->bulan_terakhir,
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
