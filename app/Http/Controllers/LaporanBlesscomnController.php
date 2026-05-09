<?php

namespace App\Http\Controllers;

use App\Models\LaporanBlesscomn;
use App\Models\MasterBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanBlesscomnController extends Controller
{
    // Menampilkan daftar laporan blesscomn
    public function index()
    {
        $laporans = LaporanBlesscomn::with(['wilayah', 'pelayanan', 'blesscomn'])
            ->latest('tanggal_pelaksanaan')
            ->get();

        return view('laporan_blesscomn.index', compact('laporans'));
    }

    // Form untuk menambah laporan blesscomn baru
    public function create()
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();

        return view('laporan_blesscomn.create', compact('wilayahs', 'pelayanans'));
    }

    // Menyimpan laporan blesscomn baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pelaksanaan' => 'required|date|before_or_equal:' . Carbon::today()->toDateString(),
            'id_wilayah'          => 'required|exists:wilayahs,id',
            'id_pelayanan'        => 'required|exists:pelayanans,id',
            'id_blesscomn'        => 'required|exists:master_blesscomns,id',
            'hadir_pria'          => 'required|integer|min:0',
            'hadir_wanita'        => 'required|integer|min:0',
            'baru_pria'           => 'required|integer|min:0',
            'baru_wanita'         => 'required|integer|min:0',
        ], [
            'tanggal_pelaksanaan.before_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari hari ini.',
        ]);

        $data = $request->only([
            'tanggal_pelaksanaan', 'id_wilayah', 'id_pelayanan', 'id_blesscomn',
            'hadir_pria', 'hadir_wanita', 'baru_pria', 'baru_wanita',
        ]);

        // Auto-kalkulasi total
        $data['total_hadir'] = (int) $request->hadir_pria + (int) $request->hadir_wanita;
        $data['total_baru'] = (int) $request->baru_pria + (int) $request->baru_wanita;

        LaporanBlesscomn::create($data);

        return redirect()->route('laporan_blesscomn.index')
            ->with('success', 'Laporan Blesscomn berhasil disimpan.');
    }

    // Form untuk mengedit laporan blesscomn
    public function edit(LaporanBlesscomn $laporanBlesscomn)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();

        // Pre-load blesscomn list yang sesuai filter wilayah & pelayanan
        $blesscomnList = MasterBlesscomn::where('id_wilayah', $laporanBlesscomn->id_wilayah)
            ->where('id_pelayanan', $laporanBlesscomn->id_pelayanan)
            ->orderBy('nama_blesscomn')
            ->get();

        return view('laporan_blesscomn.edit', compact(
            'laporanBlesscomn', 'wilayahs', 'pelayanans', 'blesscomnList'
        ));
    }

    // Mengupdate data laporan blesscomn
    public function update(Request $request, LaporanBlesscomn $laporanBlesscomn)
    {
        $request->validate([
            'tanggal_pelaksanaan' => 'required|date|before_or_equal:' . Carbon::today()->toDateString(),
            'id_wilayah'          => 'required|exists:wilayahs,id',
            'id_pelayanan'        => 'required|exists:pelayanans,id',
            'id_blesscomn'        => 'required|exists:master_blesscomns,id',
            'hadir_pria'          => 'required|integer|min:0',
            'hadir_wanita'        => 'required|integer|min:0',
            'baru_pria'           => 'required|integer|min:0',
            'baru_wanita'         => 'required|integer|min:0',
        ], [
            'tanggal_pelaksanaan.before_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari hari ini.',
        ]);

        $data = $request->only([
            'tanggal_pelaksanaan', 'id_wilayah', 'id_pelayanan', 'id_blesscomn',
            'hadir_pria', 'hadir_wanita', 'baru_pria', 'baru_wanita',
        ]);

        $data['total_hadir'] = (int) $request->hadir_pria + (int) $request->hadir_wanita;
        $data['total_baru'] = (int) $request->baru_pria + (int) $request->baru_wanita;

        $laporanBlesscomn->update($data);

        return redirect()->route('laporan_blesscomn.index')
            ->with('success', 'Laporan Blesscomn berhasil diperbarui.');
    }

    // Soft delete laporan blesscomn via AJAX
    public function destroy(LaporanBlesscomn $laporanBlesscomn)
    {
        $laporanBlesscomn->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Laporan Blesscomn berhasil dihapus.']);
        }

        return redirect()->route('laporan_blesscomn.index')
            ->with('success', 'Laporan Blesscomn berhasil dihapus.');
    }
}
