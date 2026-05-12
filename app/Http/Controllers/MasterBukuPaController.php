<?php

namespace App\Http\Controllers;

use App\Models\MasterBukuPa;
use App\Models\Role;
use Illuminate\Http\Request;

class MasterBukuPaController extends Controller
{
    // Menampilkan daftar buku PA
    public function index(Request $request)
    {
        $user = $request->user();
        $bukuPas = MasterBukuPa::with(['requester', 'reviewer'])
            ->when(! $user->isSuperadmin(), function ($query) use ($user) {
                $query->where(function ($inner) use ($user) {
                    $inner->where('status', MasterBukuPa::STATUS_APPROVED)
                        ->orWhere('requested_by', $user->id);
                });
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->orderBy('nama_buku')
            ->get();

        return view('master_buku_pa.index', compact('bukuPas'));
    }

    // Form untuk menambah buku PA baru
    public function create()
    {
        return view('master_buku_pa.create');
    }

    // Menyimpan buku PA baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'jumlah_bab' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $status = $user->isSuperadmin()
            ? MasterBukuPa::STATUS_APPROVED
            : MasterBukuPa::STATUS_PENDING;

        MasterBukuPa::create([
            'nama_buku' => strip_tags(trim($request->nama_buku)),
            'jumlah_bab' => (int) $request->jumlah_bab,
            'status' => $status,
            'requested_by' => $user->id,
            'requested_at' => now(),
            'reviewed_by' => $user->isSuperadmin() ? $user->id : null,
            'reviewed_at' => $user->isSuperadmin() ? now() : null,
        ]);

        if ($status === MasterBukuPa::STATUS_PENDING) {
            return redirect()->route('master_buku_pa.index')
                ->with('success', 'Buku PA diajukan dan menunggu persetujuan superadmin.');
        }

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil ditambahkan.');
    }

    // Form untuk mengedit buku PA
    public function edit(MasterBukuPa $masterBukuPa)
    {
        return view('master_buku_pa.edit', compact('masterBukuPa'));
    }

    // Mengupdate data buku PA
    public function update(Request $request, MasterBukuPa $masterBukuPa)
    {
        abort_unless($request->user()->isSuperadmin(), 403, 'Hanya superadmin yang dapat mengubah master buku PA.');

        $request->validate([
            'nama_buku' => 'required|string|max:255',
            'jumlah_bab' => 'required|integer|min:1',
        ]);

        $masterBukuPa->update([
            'nama_buku' => strip_tags(trim($request->nama_buku)),
            'jumlah_bab' => (int) $request->jumlah_bab,
        ]);

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil diperbarui.');
    }

    /**
     * Ticket 4: Soft Delete buku PA via AJAX.
     * Returns JSON response for SweetAlert.
     */
    public function destroy(MasterBukuPa $masterBukuPa)
    {
        abort_unless(request()->user()->isSuperadmin(), 403, 'Hanya superadmin yang dapat menghapus master buku PA.');

        $masterBukuPa->delete(); // Soft delete karena model pakai SoftDeletes

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Buku PA berhasil dihapus.']);
        }

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA berhasil dihapus.');
    }

    /**
     * API: Mengembalikan detail buku PA (untuk AJAX).
     */
    public function show(MasterBukuPa $masterBukuPa)
    {
        abort_if($masterBukuPa->status !== MasterBukuPa::STATUS_APPROVED, 404);

        return response()->json([
            'id' => $masterBukuPa->id,
            'nama_buku' => $masterBukuPa->nama_buku,
            'jumlah_bab' => $masterBukuPa->jumlah_bab,
        ]);
    }

    public function approve(Request $request, MasterBukuPa $masterBukuPa)
    {
        abort_unless($request->user()->hasRole(Role::SUPERADMIN), 403);
        abort_unless($masterBukuPa->status === MasterBukuPa::STATUS_PENDING, 422, 'Pengajuan buku ini sudah ditinjau.');

        $masterBukuPa->forceFill([
            'status' => MasterBukuPa::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $request->string('review_note')->trim()->toString() ?: null,
        ])->save();

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Buku PA disetujui dan sudah tersedia untuk laporan PA.');
    }

    public function reject(Request $request, MasterBukuPa $masterBukuPa)
    {
        abort_unless($request->user()->hasRole(Role::SUPERADMIN), 403);
        abort_unless($masterBukuPa->status === MasterBukuPa::STATUS_PENDING, 422, 'Pengajuan buku ini sudah ditinjau.');

        $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $masterBukuPa->forceFill([
            'status' => MasterBukuPa::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $request->string('review_note')->trim()->toString() ?: null,
        ])->save();

        return redirect()->route('master_buku_pa.index')
            ->with('success', 'Pengajuan buku PA ditolak.');
    }
}
