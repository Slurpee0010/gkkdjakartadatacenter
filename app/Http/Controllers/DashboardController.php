<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\AnakBimbingan;
use App\Models\LaporanPa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalWilayah = Wilayah::count();
        $totalPelayanan = Pelayanan::count();
        $totalPembimbing = Pembimbing::count();
        $totalAnakBimbingan = AnakBimbingan::count();
        $totalLaporanPa = LaporanPa::count();

        // Get recent data for the dashboard
        $recentPembimbing = Pembimbing::with(['wilayah', 'pelayanan'])
            ->latest()
            ->take(5)
            ->get();

        $recentAnakBimbingan = AnakBimbingan::with(['pembimbing', 'wilayah', 'pelayanan'])
            ->latest()
            ->take(5)
            ->get();

        $recentLaporanPa = LaporanPa::with(['pembimbing', 'anakPa', 'bukuPa'])
            ->latest('tanggal_pa')
            ->take(5)
            ->get();

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
}
