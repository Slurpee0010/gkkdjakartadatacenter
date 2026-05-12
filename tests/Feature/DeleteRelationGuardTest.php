<?php

namespace Tests\Feature;

use App\Models\AnakBimbingan;
use App\Models\LaporanPa;
use App\Models\MasterBukuPa;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteRelationGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembimbing_with_anak_bimbingan_cannot_be_deleted(): void
    {
        $this->seed(RbacSeeder::class);
        [$wilayah, $pelayanan, $pembimbing] = $this->createBasePaData();

        AnakBimbingan::create([
            'nama_anak' => 'Anak Test',
            'pembimbing_id' => $pembimbing->id,
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        $response = $this->actingAs($this->superadmin())
            ->delete(route('pembimbing.destroy', $pembimbing));

        $response->assertRedirect(route('pembimbing.index'));
        $response->assertSessionHasErrors(['delete']);
        $this->assertDatabaseHas('pembimbings', ['id' => $pembimbing->id]);
    }

    public function test_anak_bimbingan_with_laporan_pa_cannot_be_deleted(): void
    {
        $this->seed(RbacSeeder::class);
        [$wilayah, $pelayanan, $pembimbing] = $this->createBasePaData();

        $anakBimbingan = AnakBimbingan::create([
            'nama_anak' => 'Anak Test',
            'pembimbing_id' => $pembimbing->id,
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        $bukuPa = MasterBukuPa::create([
            'nama_buku' => 'Buku Test',
            'jumlah_bab' => 10,
        ]);

        LaporanPa::create([
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
            'pembimbing_id' => $pembimbing->id,
            'anak_pa_id' => $anakBimbingan->id,
            'buku_pa_id' => $bukuPa->id,
            'bab' => 1,
            'tanggal_pa' => '2026-05-01',
        ]);

        $response = $this->actingAs($this->superadmin())
            ->delete(route('anak_bimbingan.destroy', $anakBimbingan));

        $response->assertRedirect(route('anak_bimbingan.index'));
        $response->assertSessionHasErrors(['delete']);
        $this->assertDatabaseHas('anak_bimbingans', ['id' => $anakBimbingan->id]);
    }

    public function test_bulk_delete_pembimbing_deletes_selected_rows(): void
    {
        $this->seed(RbacSeeder::class);
        [$wilayah, $pelayanan, $firstPembimbing] = $this->createBasePaData();
        $secondPembimbing = Pembimbing::create([
            'nama_pembimbing' => 'Pembimbing Kedua',
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        $response = $this->actingAs($this->superadmin())
            ->deleteJson(route('pembimbing.bulk-destroy'), [
                'ids' => [$firstPembimbing->id, $secondPembimbing->id],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('pembimbings', ['id' => $firstPembimbing->id]);
        $this->assertDatabaseMissing('pembimbings', ['id' => $secondPembimbing->id]);
    }

    public function test_bulk_delete_anak_bimbingan_blocks_rows_used_by_laporan_pa(): void
    {
        $this->seed(RbacSeeder::class);
        [$wilayah, $pelayanan, $pembimbing] = $this->createBasePaData();

        $anakBimbingan = AnakBimbingan::create([
            'nama_anak' => 'Anak Test',
            'pembimbing_id' => $pembimbing->id,
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        $bukuPa = MasterBukuPa::create([
            'nama_buku' => 'Buku Test',
            'jumlah_bab' => 10,
        ]);

        LaporanPa::create([
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
            'pembimbing_id' => $pembimbing->id,
            'anak_pa_id' => $anakBimbingan->id,
            'buku_pa_id' => $bukuPa->id,
            'bab' => 1,
            'tanggal_pa' => '2026-05-01',
        ]);

        $response = $this->actingAs($this->superadmin())
            ->deleteJson(route('anak_bimbingan.bulk-destroy'), [
                'ids' => [$anakBimbingan->id],
            ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('anak_bimbingans', ['id' => $anakBimbingan->id]);
    }

    private function createBasePaData(): array
    {
        $wilayah = Wilayah::create(['nama_wilayah' => 'Wilayah Test']);
        $pelayanan = Pelayanan::create(['nama_pelayanan' => 'Pelayanan Test']);
        $pembimbing = Pembimbing::create([
            'nama_pembimbing' => 'Pembimbing Test',
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        return [$wilayah, $pelayanan, $pembimbing];
    }

    private function superadmin(): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', Role::SUPERADMIN)->value('id'),
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
