<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\AnakBimbingan;
use App\Models\AuditLog;
use App\Models\LaporanPa;
use App\Models\Pembimbing;
use App\Services\Audit\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PublicLaporanPaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($request->isMethod('get')) {
            abort(405);
        }

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
        ], [
            'tanggal_pa.before_or_equal' => 'Tanggal PA tidak boleh lebih dari hari ini.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (empty($request->input('buku_pa_id')) && trim((string) $request->input('buku_pa_lainnya')) === '') {
                $validator->errors()->add('buku_pa_lainnya', 'Nama buku PA wajib diisi jika buku tidak dipilih dari master.');
            }

            $pembimbing = Pembimbing::find($request->input('pembimbing_id'));
            if ($pembimbing && ((int) $pembimbing->wilayah_id !== (int) $request->input('wilayah_id')
                || (int) $pembimbing->pelayanan_id !== (int) $request->input('pelayanan_id'))) {
                $validator->errors()->add('pembimbing_id', 'Pembimbing tidak sesuai dengan wilayah dan pelayanan.');
            }

            $anakCount = AnakBimbingan::whereIn('id', (array) $request->input('anak_pa_ids', []))
                ->where('pembimbing_id', $request->input('pembimbing_id'))
                ->where('wilayah_id', $request->input('wilayah_id'))
                ->where('pelayanan_id', $request->input('pelayanan_id'))
                ->count();

            if ($anakCount !== count((array) $request->input('anak_pa_ids', []))) {
                $validator->errors()->add('anak_pa_ids', 'Ada anak PA yang tidak sesuai dengan pembimbing, wilayah, atau pelayanan.');
            }
        });

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
            'auditable_label' => "Public Batch Laporan PA ({$created} anak)",
            'metadata' => [
                'created_count' => $created,
                'source' => 'public_endpoint',
                'wilayah_id' => (int) $validated['wilayah_id'],
                'pelayanan_id' => (int) $validated['pelayanan_id'],
                'pembimbing_id' => (int) $validated['pembimbing_id'],
            ],
        ]);

        return response()->json([
            'message' => "Laporan PA berhasil dikirim untuk {$created} anak PA.",
            'created_count' => $created,
        ], 201);
    }
}
