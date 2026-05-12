<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\LaporanBlesscomn;
use App\Models\MasterBlesscomn;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicLaporanBlesscomnController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($request->isMethod('get')) {
            abort(405);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_pelaksanaan' => ['required', 'date', 'before_or_equal:' . Carbon::today()->toDateString()],
            'id_wilayah' => ['required', 'integer', 'exists:wilayahs,id'],
            'id_pelayanan' => ['required', 'integer', 'exists:pelayanans,id'],
            'id_blesscomn' => ['required', 'integer', 'exists:master_blesscomns,id'],
            'hadir_pria' => ['required', 'integer', 'min:0', 'max:100000'],
            'hadir_wanita' => ['required', 'integer', 'min:0', 'max:100000'],
            'baru_pria' => ['required', 'integer', 'min:0', 'max:100000'],
            'baru_wanita' => ['required', 'integer', 'min:0', 'max:100000'],
        ], [
            'tanggal_pelaksanaan.before_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari hari ini.',
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

        $laporan = LaporanBlesscomn::create($payload);

        return response()->json([
            'message' => 'Laporan Blesscomn berhasil dikirim.',
            'data' => $laporan,
        ], 201);
    }
}
