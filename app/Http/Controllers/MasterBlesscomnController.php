<?php

namespace App\Http\Controllers;

use App\Models\MasterBlesscomn;
use App\Models\PengurusBlesscomn;
use App\Models\Wilayah;
use App\Models\Pelayanan;
use App\Support\Exports\SimpleTableExporter;
use Illuminate\Http\Request;

class MasterBlesscomnController extends Controller
{
    // Menampilkan daftar master blesscomn
    public function index(Request $request)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $blesscomns = $this->buildIndexQuery($request)
            ->latest()
            ->get();

        return view('master_blesscomn.index', compact('blesscomns', 'wilayahs', 'pelayanans'));
    }

    // Form untuk menambah master blesscomn baru
    public function create()
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        // Pengurus dikirim beserta nama_asisten untuk UI logic auto-fill
        $pengurusList = PengurusBlesscomn::orderBy('nama_ketua')
            ->get(['id', 'nama_ketua', 'nama_asisten']);
        $blesscomnList = MasterBlesscomn::orderBy('nama_blesscomn')->get(['id', 'nama_blesscomn']);

        return view('master_blesscomn.create', compact(
            'wilayahs', 'pelayanans', 'pengurusList', 'blesscomnList'
        ));
    }

    // Menyimpan master blesscomn baru ke database
    public function store(Request $request)
    {
        $rules = [
            'nama_blesscomn'    => 'required|string|max:255',
            'tanggal_terbentuk' => 'required|date',
            'id_pengurus'       => 'required|exists:pengurus_blesscomns,id',
            'id_wilayah'        => 'required|exists:wilayahs,id',
            'id_pelayanan'      => 'required|exists:pelayanans,id',
            'is_pembelahan'     => 'required|boolean',
        ];

        // Conditional: jika is_pembelahan true, id_blesscomn_induk wajib
        if ($request->boolean('is_pembelahan')) {
            $rules['id_blesscomn_induk'] = 'required|exists:master_blesscomns,id';
        }

        $request->validate($rules);

        $data = $request->only([
            'nama_blesscomn', 'tanggal_terbentuk', 'id_pengurus',
            'id_wilayah', 'id_pelayanan', 'is_pembelahan',
        ]);

        // Set id_blesscomn_induk hanya jika pembelahan
        $data['id_blesscomn_induk'] = $request->boolean('is_pembelahan')
            ? $request->id_blesscomn_induk
            : null;

        MasterBlesscomn::create($data);

        return redirect()->route('master_blesscomn.index')
            ->with('success', 'Master Blesscomn berhasil ditambahkan.');
    }

    // Form untuk mengedit master blesscomn
    public function edit(MasterBlesscomn $masterBlesscomn)
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $pelayanans = Pelayanan::orderBy('nama_pelayanan')->get();
        $pengurusList = PengurusBlesscomn::orderBy('nama_ketua')
            ->get(['id', 'nama_ketua', 'nama_asisten']);
        // Exclude self dari list induk
        $blesscomnList = MasterBlesscomn::where('id', '!=', $masterBlesscomn->id)
            ->orderBy('nama_blesscomn')
            ->get(['id', 'nama_blesscomn']);

        return view('master_blesscomn.edit', compact(
            'masterBlesscomn', 'wilayahs', 'pelayanans', 'pengurusList', 'blesscomnList'
        ));
    }

    // Mengupdate data master blesscomn
    public function update(Request $request, MasterBlesscomn $masterBlesscomn)
    {
        $rules = [
            'nama_blesscomn'    => 'required|string|max:255',
            'tanggal_terbentuk' => 'required|date',
            'id_pengurus'       => 'required|exists:pengurus_blesscomns,id',
            'id_wilayah'        => 'required|exists:wilayahs,id',
            'id_pelayanan'      => 'required|exists:pelayanans,id',
            'is_pembelahan'     => 'required|boolean',
        ];

        if ($request->boolean('is_pembelahan')) {
            $rules['id_blesscomn_induk'] = 'required|exists:master_blesscomns,id';
        }

        $request->validate($rules);

        $data = $request->only([
            'nama_blesscomn', 'tanggal_terbentuk', 'id_pengurus',
            'id_wilayah', 'id_pelayanan', 'is_pembelahan',
        ]);

        $data['id_blesscomn_induk'] = $request->boolean('is_pembelahan')
            ? $request->id_blesscomn_induk
            : null;

        $masterBlesscomn->update($data);

        return redirect()->route('master_blesscomn.index')
            ->with('success', 'Master Blesscomn berhasil diperbarui.');
    }

    // Soft delete master blesscomn via AJAX
    public function destroy(MasterBlesscomn $masterBlesscomn)
    {
        $masterBlesscomn->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Master Blesscomn berhasil dihapus.']);
        }

        return redirect()->route('master_blesscomn.index')
            ->with('success', 'Master Blesscomn berhasil dihapus.');
    }

    /**
     * Export daftar Master Blesscomn ke CSV atau Excel.
     */
    public function export(Request $request)
    {
        $blesscomns = $this->buildIndexQuery($request)->latest()->get();

        return SimpleTableExporter::download(
            'master_blesscomn',
            ['Tanggal Terbentuk', 'Nama Blesscomn', 'Ketua', 'Asisten', 'Wilayah', 'Pelayanan', 'Pembelahan', 'Blesscomn Induk'],
            $blesscomns,
            fn (MasterBlesscomn $item) => [
                optional($item->tanggal_terbentuk)->format('Y-m-d'),
                $item->nama_blesscomn,
                $item->pengurus->nama_ketua ?? '-',
                $item->pengurus->nama_asisten ?? '-',
                $item->wilayah->nama_wilayah ?? '-',
                $item->pelayanan->nama_pelayanan ?? '-',
                $item->is_pembelahan ? 'Ya' : 'Tidak',
                $item->blesscomnInduk->nama_blesscomn ?? '-',
            ],
            $request->get('format', 'csv')
        );
    }

    /**
     * Query builder untuk daftar Master Blesscomn.
     */
    private function buildIndexQuery(Request $request)
    {
        $query = MasterBlesscomn::with(['pengurus', 'wilayah', 'pelayanan', 'blesscomnInduk']);

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_terbentuk', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_terbentuk', '<=', $request->date_to);
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
                $subQuery->where('nama_blesscomn', 'like', "%{$search}%")
                    ->orWhereHas('pengurus', function ($relation) use ($search) {
                        $relation->where('nama_ketua', 'like', "%{$search}%")
                            ->orWhere('nama_asisten', 'like', "%{$search}%");
                    })
                    ->orWhereHas('blesscomnInduk', fn ($relation) => $relation->where('nama_blesscomn', 'like', "%{$search}%"))
                    ->orWhereHas('wilayah', fn ($relation) => $relation->where('nama_wilayah', 'like', "%{$search}%"))
                    ->orWhereHas('pelayanan', fn ($relation) => $relation->where('nama_pelayanan', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    // =========================================
    // API: Mendapatkan Blesscomn berdasarkan Wilayah & Pelayanan
    // =========================================
    public function getBlesscomnByFilter(Request $request)
    {
        $query = MasterBlesscomn::query();

        if ($request->filled('id_wilayah')) {
            $query->where('id_wilayah', $request->id_wilayah);
        }
        if ($request->filled('id_pelayanan')) {
            $query->where('id_pelayanan', $request->id_pelayanan);
        }

        $blesscomns = $query->orderBy('nama_blesscomn')->get(['id', 'nama_blesscomn']);

        return response()->json($blesscomns);
    }
}
