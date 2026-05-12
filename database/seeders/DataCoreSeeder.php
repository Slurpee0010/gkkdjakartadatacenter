<?php

namespace Database\Seeders;

use App\Models\AnakBimbingan;
use App\Models\MasterBlesscomn;
use App\Models\MasterBukuPa;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\PengurusBlesscomn;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class DataCoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahs = collect(['Pusat', 'Barat', 'Timur', 'Utara', 'Selatan'])
            ->mapWithKeys(fn (string $name) => [
                $name => Wilayah::firstOrCreate(['nama_wilayah' => $name]),
            ]);

        $pelayanans = collect(['Umum', 'Youth', 'Dewasa Muda', 'Keluarga'])
            ->mapWithKeys(fn (string $name) => [
                $name => Pelayanan::firstOrCreate(['nama_pelayanan' => $name]),
            ]);

        $admin = User::where('email', env('SUPERADMIN_EMAIL', 'superadmin@gkkdjakarta.local'))->first();

        foreach ([
            ['Buku PA Dasar', 8],
            ['Buku PA Bertumbuh', 10],
            ['Buku PA Pemuridan', 12],
        ] as [$name, $chapters]) {
            MasterBukuPa::updateOrCreate(
                ['nama_buku' => $name],
                [
                    'jumlah_bab' => $chapters,
                    'status' => MasterBukuPa::STATUS_APPROVED,
                    'requested_by' => $admin?->id,
                    'reviewed_by' => $admin?->id,
                    'requested_at' => now(),
                    'reviewed_at' => now(),
                ]
            );
        }

        $pembimbingData = [
            ['Andi Santoso', 'Pusat', 'Umum', ['Budi Pratama', 'Citra Lestari']],
            ['Maria Wijaya', 'Barat', 'Youth', ['Daniel Putra', 'Elisa Natalia']],
            ['Yohanes Tan', 'Timur', 'Dewasa Muda', ['Felix Jonathan', 'Grace Amelia']],
        ];

        foreach ($pembimbingData as [$name, $wilayahName, $pelayananName, $anakNames]) {
            $pembimbing = Pembimbing::updateOrCreate(
                ['nama_pembimbing' => $name],
                [
                    'wilayah_id' => $wilayahs[$wilayahName]->id,
                    'pelayanan_id' => $pelayanans[$pelayananName]->id,
                ]
            );

            foreach ($anakNames as $anakName) {
                AnakBimbingan::updateOrCreate(
                    ['nama_anak' => $anakName],
                    [
                        'pembimbing_id' => $pembimbing->id,
                        'wilayah_id' => $pembimbing->wilayah_id,
                        'pelayanan_id' => $pembimbing->pelayanan_id,
                    ]
                );
            }
        }

        $blesscomnData = [
            ['Blesscomn Pusat 1', 'Samuel Hartono', '081200000001', 'Rina Hartono', '081200000002', 'Pusat', 'Umum'],
            ['Blesscomn Barat Youth', 'Kevin Halim', '081200000003', 'Monica Tan', '081200000004', 'Barat', 'Youth'],
            ['Blesscomn Timur DM', 'Jonathan Lee', '081200000005', 'Clara Wijaya', '081200000006', 'Timur', 'Dewasa Muda'],
        ];

        foreach ($blesscomnData as [$blesscomnName, $ketua, $waKetua, $asisten, $waAsisten, $wilayahName, $pelayananName]) {
            $pengurus = PengurusBlesscomn::updateOrCreate(
                ['nama_ketua' => $ketua],
                [
                    'no_wa_ketua' => $waKetua,
                    'nama_asisten' => $asisten,
                    'no_wa_asisten' => $waAsisten,
                    'id_wilayah' => $wilayahs[$wilayahName]->id,
                    'id_pelayanan' => $pelayanans[$pelayananName]->id,
                ]
            );

            MasterBlesscomn::updateOrCreate(
                ['nama_blesscomn' => $blesscomnName],
                [
                    'tanggal_terbentuk' => now()->subMonths(6)->toDateString(),
                    'id_pengurus' => $pengurus->id,
                    'id_wilayah' => $pengurus->id_wilayah,
                    'id_pelayanan' => $pengurus->id_pelayanan,
                    'is_pembelahan' => false,
                    'id_blesscomn_induk' => null,
                ]
            );
        }
    }
}
