<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\AnakBimbingan;
use App\Models\LaporanPa;
use App\Services\Rbac\DataScope;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $scope = $this->dataScope();

        $totalWilayah = $scope->scopedWilayahId($request->user()) !== null ? 1 : Wilayah::count();
        $totalPelayanan = Pelayanan::count();
        $totalPembimbing = $scope->applyToRequestQuery(Pembimbing::query(), $request, 'wilayah_id')->count();
        $totalAnakBimbingan = $scope->applyToRequestQuery(AnakBimbingan::query(), $request, 'wilayah_id')->count();
        $totalLaporanPa = $scope->applyToRequestQuery(LaporanPa::query(), $request, 'wilayah_id')->count();

        // Get recent data for the dashboard
        $recentPembimbingQuery = Pembimbing::with(['wilayah', 'pelayanan'])->latest();
        $scope->applyToRequestQuery($recentPembimbingQuery, $request, 'wilayah_id');
        $recentPembimbing = $recentPembimbingQuery->take(5)->get();

        $recentAnakBimbinganQuery = AnakBimbingan::with(['pembimbing', 'wilayah', 'pelayanan'])->latest();
        $scope->applyToRequestQuery($recentAnakBimbinganQuery, $request, 'wilayah_id');
        $recentAnakBimbingan = $recentAnakBimbinganQuery->take(5)->get();

        $recentLaporanPaQuery = LaporanPa::with(['pembimbing', 'anakPa', 'bukuPa'])->latest('tanggal_pa');
        $scope->applyToRequestQuery($recentLaporanPaQuery, $request, 'wilayah_id');
        $recentLaporanPa = $recentLaporanPaQuery->take(5)->get();

        return view('dashboard', compact(
            'totalWilayah',
            'totalPelayanan',
            'totalPembimbing',
            'totalAnakBimbingan',
            'totalLaporanPa',
            'recentPembimbing',
            'recentAnakBimbingan',
            'recentLaporanPa'
        ));
    }

    private function dataScope(): DataScope
    {
        return app(DataScope::class);
    }
}
