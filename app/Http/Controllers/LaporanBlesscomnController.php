<?php

namespace App\Http\Controllers;

use App\Models\LaporanBlesscomn;
use App\Models\MasterBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanBlesscomnController extends Controller
{
    // Menampilkan daftar laporan blesscomn
    public function index(Request $request)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $laporans = $this->buildIndexQuery($request)
            ->latest('tanggal_pelaksanaan')
            ->get();

        return view('laporan_blesscomn.index', compact('laporans', 'wilayahs', 'pelayanans'));
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

    /**
     * Export daftar Laporan Blesscomn ke CSV atau Excel.
     */
    public function export(Request $request)
    {
        $laporans = $this->buildIndexQuery($request)
            ->latest('tanggal_pelaksanaan')
            ->get();

        return SimpleTableExporter::download(
            'laporan_blesscomn',
            ['Tanggal', 'Blesscomn', 'Wilayah', 'Pelayanan', 'Hadir Pria', 'Hadir Wanita', 'Total Hadir', 'Baru Pria', 'Baru Wanita', 'Total Baru'],
            $laporans,
            fn (LaporanBlesscomn $item) => [
                optional($item->tanggal_pelaksanaan)->format('Y-m-d'),
                $item->blesscomn->nama_blesscomn ?? '-',
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
                $item->hadir_pria,
                $item->hadir_wanita,
                $item->total_hadir,
                $item->baru_pria,
                $item->baru_wanita,
                $item->total_baru,
            ],
            $request->get('format', 'csv')
        );
    }

    /**
     * Query builder untuk daftar Laporan Blesscomn.
     */
    private function buildIndexQuery(Request $request)
    {
        $query = LaporanBlesscomn::with(['wilayah', 'pelayanan', 'blesscomn']);

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pelaksanaan', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pelaksanaan', '<=', $request->date_to);
        }
        if ($request->filled('id_wilayah')) {
            $query->where('id_wilayah', $request->id_wilayah);
        }
        if ($request->filled('id_pelayanan')) {
            $query->where('id_pelayanan', $request->id_pelayanan);
        }

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('blesscomn', fn ($relation) => $relation->where('nama_blesscomn', 'like', "%{$search}%"))
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"))
                    ->orWhere('hadir_pria', 'like', "%{$search}%")
                    ->orWhere('hadir_wanita', 'like', "%{$search}%")
                    ->orWhere('total_hadir', 'like', "%{$search}%")
                    ->orWhere('baru_pria', 'like', "%{$search}%")
                    ->orWhere('baru_wanita', 'like', "%{$search}%")
                    ->orWhere('total_baru', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
