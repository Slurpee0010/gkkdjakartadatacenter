<?php

namespace App\Http\Controllers;

use App\Models\AnakBimbingan;
use App\Models\AuditLog;
use App\Models\LaporanBlesscomn;
use App\Models\LaporanPa;
use App\Models\MasterBlesscomn;
use App\Models\MasterBukuPa;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\Wilayah;
use App\Services\Audit\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    public function index(): View
    {
        return view('public.dashboard');
    }

    public function laporanPa(): View
    {
        return view('public.laporan-pa', [
            'wilayahs' => Wilayah::orderBy('nama_wilayah')->get(),
            'pelayanans' => Pelayanan::orderBy('nama_pelayanan')->get(),
            'bukuPas' => MasterBukuPa::approved()->orderBy('nama_buku')->get(),
        ]);
    }

    public function storeLaporanPa(Request $request): RedirectResponse
    {
        if ($request->input('buku_pa_id') === 'lainnya') {
            $request->merge(['buku_pa_id' => null]);
        }

        $validator = Validator::make($request->all(), [
            'wilayah_id' => ['required', 'integer', 'exists:wilayahs,id'],
            'pelayanan_id' => ['required', 'integer', 'exists:pelayanans,id'],
            'pembimbing_id' => ['required', 'integer', 'exists:pembimbings,id'],
            'anak_pa_ids' => ['required', 'array', 'min:1', 'max:30'],
            'anak_pa_ids.*' => ['required', 'integer', 'distinct', 'exists:anak_bimbingans,id'],
            'buku_pa_id' => ['nullable', 'integer', 'exists:master_buku_pas,id'],
            'buku_pa_lainnya' => ['nullable', 'string', 'max:255'],
            'bab' => ['required', 'integer', 'min:1', 'max:500'],
            'tanggal_pa' => ['required', 'date', 'before_or_equal:' . Carbon::today()->toDateString()],
        ]);

        $validator->after(fn ($validator) => $this->validatePaPayload($validator, $request));
        $validated = $validator->validate();

        $created = DB::transaction(function () use ($validated) {
            $sharedData = [
                'wilayah_id' => (int) $validated['wilayah_id'],
                'pelayanan_id' => (int) $validated['pelayanan_id'],
                'pembimbing_id' => (int) $validated['pembimbing_id'],
                'buku_pa_id' => $validated['buku_pa_id'] ?? null,
                'buku_pa_lainnya' => empty($validated['buku_pa_id']) ? strip_tags(trim($validated['buku_pa_lainnya'])) : null,
                'bab' => (int) $validated['bab'],
                'tanggal_pa' => $validated['tanggal_pa'],
            ];

            $now = now();
            $rows = collect($validated['anak_pa_ids'])
                ->map(fn ($anakPaId) => array_merge($sharedData, [
                    'anak_pa_id' => (int) $anakPaId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]))
                ->all();

            LaporanPa::insert($rows);

            return count($rows);
        });

        app(AuditLogger::class)->log(AuditLog::EVENT_CREATED, [
            'module' => 'pa',
            'auditable_type' => LaporanPa::class,
            'auditable_label' => "Public Dashboard Laporan PA ({$created} anak)",
            'metadata' => ['source' => 'dashboard_utama', 'created_count' => $created],
        ]);

        return redirect()
            ->route('public.laporan-pa')
            ->with('success', "Laporan PA berhasil dikirim untuk {$created} anak PA.");
    }

    public function laporanBlesscomn(): View
    {
        return view('public.laporan-blesscomn', [
            'wilayahs' => Wilayah::orderBy('nama_wilayah')->get(),
            'pelayanans' => Pelayanan::orderBy('nama_pelayanan')->get(),
        ]);
    }

    public function storeLaporanBlesscomn(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pelaksanaan' => ['required', 'date', 'before_or_equal:' . Carbon::today()->toDateString()],
            'id_wilayah' => ['required', 'integer', 'exists:wilayahs,id'],
            'id_pelayanan' => ['required', 'integer', 'exists:pelayanans,id'],
            'id_blesscomn' => ['required', 'integer', 'exists:master_blesscomns,id'],
            'hadir_pria' => ['required', 'integer', 'min:0', 'max:100000'],
            'hadir_wanita' => ['required', 'integer', 'min:0', 'max:100000'],
            'baru_pria' => ['required', 'integer', 'min:0', 'max:100000'],
            'baru_wanita' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $blesscomn = MasterBlesscomn::find($request->input('id_blesscomn'));

            if ($blesscomn && ((int) $blesscomn->id_wilayah !== (int) $request->input('id_wilayah')
                || (int) $blesscomn->id_pelayanan !== (int) $request->input('id_pelayanan'))) {
                $validator->errors()->add('id_blesscomn', 'Blesscomn tidak sesuai dengan wilayah dan pelayanan.');
            }
        });

        $validated = $validator->validate();

        $payload = [
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
            'id_wilayah' => (int) $validated['id_wilayah'],
            'id_pelayanan' => (int) $validated['id_pelayanan'],
            'id_blesscomn' => (int) $validated['id_blesscomn'],
            'hadir_pria' => (int) $validated['hadir_pria'],
            'hadir_wanita' => (int) $validated['hadir_wanita'],
            'baru_pria' => (int) $validated['baru_pria'],
            'baru_wanita' => (int) $validated['baru_wanita'],
        ];
        $payload['total_hadir'] = $payload['hadir_pria'] + $payload['hadir_wanita'];
        $payload['total_baru'] = $payload['baru_pria'] + $payload['baru_wanita'];

        LaporanBlesscomn::create($payload);

        return redirect()
            ->route('public.laporan-blesscomn')
            ->with('success', 'Laporan Blesscomn berhasil dikirim.');
    }

    public function getPembimbing(Request $request): JsonResponse
    {
        $request->validate([
            'wilayah_id' => ['required', 'integer', 'exists:wilayahs,id'],
            'pelayanan_id' => ['required', 'integer', 'exists:pelayanans,id'],
        ]);

        return response()->json(Pembimbing::where('wilayah_id', $request->integer('wilayah_id'))
            ->where('pelayanan_id', $request->integer('pelayanan_id'))
            ->orderBy('nama_pembimbing')
            ->get(['id', 'nama_pembimbing']));
    }

    public function getAnakPa(Request $request): JsonResponse
    {
        $request->validate([
            'pembimbing_id' => ['required', 'integer', 'exists:pembimbings,id'],
        ]);

        return response()->json(AnakBimbingan::where('pembimbing_id', $request->integer('pembimbing_id'))
            ->orderBy('nama_anak')
            ->get(['id', 'nama_anak']));
    }

    public function getBlesscomn(Request $request): JsonResponse
    {
        $request->validate([
            'id_wilayah' => ['required', 'integer', 'exists:wilayahs,id'],
            'id_pelayanan' => ['required', 'integer', 'exists:pelayanans,id'],
        ]);

        return response()->json(MasterBlesscomn::where('id_wilayah', $request->integer('id_wilayah'))
            ->where('id_pelayanan', $request->integer('id_pelayanan'))
            ->orderBy('nama_blesscomn')
            ->get(['id', 'nama_blesscomn']));
    }

    private function validatePaPayload($validator, Request $request): void
    {
        if (empty($request->input('buku_pa_id')) && trim((string) $request->input('buku_pa_lainnya')) === '') {
            $validator->errors()->add('buku_pa_lainnya', 'Nama buku PA wajib diisi jika buku tidak dipilih dari master.');
        }

        if ($request->filled('buku_pa_id')) {
            $approved = MasterBukuPa::approved()->whereKey($request->input('buku_pa_id'))->exists();
            if (! $approved) {
                $validator->errors()->add('buku_pa_id', 'Buku PA belum disetujui superadmin.');
            }
        }

        $pembimbing = Pembimbing::find($request->input('pembimbing_id'));
        if ($pembimbing && ((int) $pembimbing->wilayah_id !== (int) $request->input('wilayah_id')
            || (int) $pembimbing->pelayanan_id !== (int) $request->input('pelayanan_id'))) {
            $validator->errors()->add('pembimbing_id', 'Pembimbing tidak sesuai dengan wilayah dan pelayanan.');
        }

        $anakIds = (array) $request->input('anak_pa_ids', []);
        $anakCount = AnakBimbingan::whereIn('id', $anakIds)
            ->where('pembimbing_id', $request->input('pembimbing_id'))
            ->where('wilayah_id', $request->input('wilayah_id'))
            ->where('pelayanan_id', $request->input('pelayanan_id'))
            ->count();

        if ($anakCount !== count($anakIds)) {
            $validator->errors()->add('anak_pa_ids', 'Ada anak PA yang tidak sesuai dengan pembimbing, wilayah, atau pelayanan.');
        }
    }
}
