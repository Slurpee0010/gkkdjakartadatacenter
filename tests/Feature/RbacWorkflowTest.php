<?php

namespace Tests\Feature;

use App\Models\LaporanPa;
use App\Models\MasterBukuPa;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDeletionRequest;
use App\Models\Wilayah;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pusat_delete_user_creates_pending_deletion_request(): void
    {
        $this->seed(RbacSeeder::class);
        $wilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Barat']);
        $adminPusat = $this->makeUser(Role::ADMIN_PUSAT);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH, ['wilayah_id' => $wilayah->id]);

        $response = $this->actingAs($adminPusat)->deleteJson(
            route('api.admin.users.destroy', $adminWilayah),
            ['reason' => 'Duplikat akun']
        );

        $response->assertAccepted()
            ->assertJsonPath('data.status', UserDeletionRequest::PENDING);

        $this->assertDatabaseHas('users', [
            'id' => $adminWilayah->id,
            'status' => User::STATUS_PENDING_DELETION,
            'deletion_requested_by' => $adminPusat->id,
        ]);
        $this->assertDatabaseHas('user_deletion_requests', [
            'user_id' => $adminWilayah->id,
            'requested_by' => $adminPusat->id,
            'status' => UserDeletionRequest::PENDING,
        ]);
    }

    public function test_superadmin_can_approve_pending_user_deletion(): void
    {
        $this->seed(RbacSeeder::class);
        $wilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Barat']);
        $superadmin = $this->makeUser(Role::SUPERADMIN);
        $adminPusat = $this->makeUser(Role::ADMIN_PUSAT);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH, ['wilayah_id' => $wilayah->id]);

        $deleteResponse = $this->actingAs($adminPusat)->deleteJson(route('api.admin.users.destroy', $adminWilayah));
        $requestId = $deleteResponse->json('data.id');

        $response = $this->actingAs($superadmin)->postJson(
            route('api.admin.user-deletion-requests.approve', $requestId),
            ['note' => 'Disetujui']
        );

        $response->assertOk()
            ->assertJsonPath('data.status', UserDeletionRequest::APPROVED);

        $this->assertSoftDeleted('users', ['id' => $adminWilayah->id]);
        $this->assertDatabaseHas('users', [
            'id' => $adminWilayah->id,
            'status' => User::STATUS_DELETED,
        ]);
    }

    public function test_admin_wilayah_only_sees_laporan_pa_from_own_region(): void
    {
        $this->seed(RbacSeeder::class);

        $ownWilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Barat']);
        $otherWilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Timur']);
        $pelayanan = Pelayanan::create(['nama_pelayanan' => 'Umum']);
        $buku = MasterBukuPa::create(['nama_buku' => 'Buku Dasar', 'jumlah_bab' => 10]);
        $ownPembimbing = Pembimbing::create([
            'nama_pembimbing' => 'Pembimbing Barat',
            'wilayah_id' => $ownWilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);
        $otherPembimbing = Pembimbing::create([
            'nama_pembimbing' => 'Pembimbing Timur',
            'wilayah_id' => $otherWilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ]);

        LaporanPa::create([
            'wilayah_id' => $ownWilayah->id,
            'pelayanan_id' => $pelayanan->id,
            'pembimbing_id' => $ownPembimbing->id,
            'anak_pa_id' => $this->anakPa($ownWilayah, $pelayanan, $ownPembimbing),
            'buku_pa_id' => $buku->id,
            'bab' => 1,
            'tanggal_pa' => '2026-05-01',
        ]);
        LaporanPa::create([
            'wilayah_id' => $otherWilayah->id,
            'pelayanan_id' => $pelayanan->id,
            'pembimbing_id' => $otherPembimbing->id,
            'anak_pa_id' => $this->anakPa($otherWilayah, $pelayanan, $otherPembimbing),
            'buku_pa_id' => $buku->id,
            'bab' => 2,
            'tanggal_pa' => '2026-05-02',
        ]);

        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH, ['wilayah_id' => $ownWilayah->id]);

        $response = $this->actingAs($adminWilayah)->get(route('laporan_pa.index'));

        $response->assertOk();
        $response->assertSee('Pembimbing Barat');
        $response->assertDontSee('Pembimbing Timur');
    }

    public function test_admin_pusat_is_forbidden_from_master_data_routes(): void
    {
        $this->seed(RbacSeeder::class);
        $adminPusat = $this->makeUser(Role::ADMIN_PUSAT);

        $response = $this->actingAs($adminPusat)->get(route('wilayah.index'));

        $response->assertForbidden();
    }

    public function test_password_change_is_audited_without_storing_plain_password(): void
    {
        $this->seed(RbacSeeder::class);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH);

        $response = $this->actingAs($adminWilayah)->put(route('password.update'), [
            'current_password' => 'password',
            'new_password' => 'NewPassword#2026',
            'new_password_confirmation' => 'NewPassword#2026',
        ]);

        $response->assertRedirect(route('password.edit'));

        $log = AuditLog::where('event', AuditLog::EVENT_PASSWORD_CHANGED)
            ->where('actor_id', $adminWilayah->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringNotContainsString('NewPassword#2026', json_encode($log->toArray()));
        $this->assertSame('[redacted]', $log->metadata['password_value'] ?? null);
    }

    public function test_only_superadmin_can_view_audit_logs(): void
    {
        $this->seed(RbacSeeder::class);
        $superadmin = $this->makeUser(Role::SUPERADMIN);
        $adminPusat = $this->makeUser(Role::ADMIN_PUSAT);

        $this->actingAs($adminPusat)
            ->get(route('audit_logs.index'))
            ->assertForbidden();

        $this->actingAs($superadmin)
            ->get(route('audit_logs.index'))
            ->assertOk();
    }

    public function test_superadmin_can_create_user_from_web_crud(): void
    {
        $this->seed(RbacSeeder::class);
        $wilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Pusat']);
        $superadmin = $this->makeUser(Role::SUPERADMIN, ['wilayah_id' => $wilayah->id]);

        $response = $this->actingAs($superadmin)->post(route('users.store'), [
            'name' => 'Admin Pusat Baru',
            'email' => 'admin.pusat.baru@example.test',
            'id_role' => Role::ADMIN_PUSAT,
            'id_wilayah' => $wilayah->id,
            'password' => 'StrongPass#2026',
            'password_confirmation' => 'StrongPass#2026',
        ]);

        $response->assertRedirect(route('users.index'));

        $created = User::where('email', 'admin.pusat.baru@example.test')->firstOrFail();

        $this->assertNotNull($created->uuid);
        $this->assertSame(Role::ADMIN_PUSAT, $created->role->name);
        $this->assertSame($wilayah->id, $created->wilayah_id);
    }

    public function test_superadmin_can_impersonate_and_stop_impersonation(): void
    {
        $this->seed(RbacSeeder::class);
        $wilayah = Wilayah::create(['nama_wilayah' => 'Jakarta Selatan']);
        $superadmin = $this->makeUser(Role::SUPERADMIN, ['wilayah_id' => $wilayah->id]);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH, ['wilayah_id' => $wilayah->id]);

        $this->actingAs($superadmin)
            ->post(route('users.impersonate', $adminWilayah))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('impersonator_id', $superadmin->id);

        $this->assertAuthenticatedAs($adminWilayah);

        $this->post(route('impersonate.stop'))
            ->assertRedirect(route('users.index'));

        $this->assertAuthenticatedAs($superadmin);
        $this->assertDatabaseHas('audit_logs', [
            'event' => AuditLog::EVENT_IMPERSONATION_STARTED,
            'actor_id' => $superadmin->id,
        ]);
    }

    public function test_public_user_role_cannot_login_to_web_session(): void
    {
        $this->seed(RbacSeeder::class);
        $guestRoleUser = $this->makeUser(Role::USER);

        $this->post(route('login.store'), [
            'email' => $guestRoleUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_book_creation_requires_superadmin_approval(): void
    {
        $this->seed(RbacSeeder::class);
        $adminPusat = $this->makeUser(Role::ADMIN_PUSAT);
        $superadmin = $this->makeUser(Role::SUPERADMIN);

        $this->actingAs($adminPusat)->post(route('master_buku_pa.store'), [
            'nama_buku' => 'Buku Menunggu Approval',
            'jumlah_bab' => 12,
        ])->assertRedirect(route('master_buku_pa.index'));

        $book = MasterBukuPa::where('nama_buku', 'Buku Menunggu Approval')->firstOrFail();

        $this->assertSame(MasterBukuPa::STATUS_PENDING, $book->status);
        $this->assertSame($adminPusat->id, $book->requested_by);

        $this->actingAs($superadmin)
            ->post(route('master_buku_pa.approve', $book))
            ->assertRedirect(route('master_buku_pa.index'));

        $this->assertSame(MasterBukuPa::STATUS_APPROVED, $book->fresh()->status);
    }

    public function test_superadmin_broadcast_targets_admin_roles_without_user_role(): void
    {
        $this->seed(RbacSeeder::class);
        $superadmin = $this->makeUser(Role::SUPERADMIN);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH);

        $this->actingAs($superadmin)->post(route('notifications.store'), [
            'title' => 'Info Internal',
            'message' => 'Pesan untuk admin.',
        ])->assertRedirect(route('notifications.index'));

        $notification = AppNotification::latest('sent_at')->firstOrFail();

        $this->assertSame([Role::SUPERADMIN, Role::ADMIN_PUSAT, Role::ADMIN_WILAYAH], $notification->target_roles);
        $this->assertNotContains(Role::USER, $notification->target_roles);

        $this->actingAs($superadmin)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Info Internal');

        $this->actingAs($adminWilayah)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Info Internal');
    }

    public function test_admin_wilayah_cannot_broadcast_notifications(): void
    {
        $this->seed(RbacSeeder::class);
        $adminWilayah = $this->makeUser(Role::ADMIN_WILAYAH);

        $this->actingAs($adminWilayah)
            ->get(route('notifications.create'))
            ->assertForbidden();

        $this->actingAs($adminWilayah)
            ->post(route('notifications.store'), [
                'title' => 'Tidak boleh',
                'message' => 'Admin wilayah tidak boleh broadcast.',
            ])
            ->assertForbidden();
    }

    public function test_public_dashboard_utama_is_accessible_without_login(): void
    {
        $this->get(route('public.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Utama')
            ->assertSee('Laporan Blesscomn')
            ->assertSee('Laporan PA');

        $this->get(route('public.laporan-pa'))->assertOk();
        $this->get(route('public.laporan-blesscomn'))->assertOk();
    }

    private function makeUser(string $roleName, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => Role::where('name', $roleName)->value('id'),
            'status' => User::STATUS_ACTIVE,
        ], $attributes));
    }

    private function anakPa(Wilayah $wilayah, Pelayanan $pelayanan, Pembimbing $pembimbing): int
    {
        return \App\Models\AnakBimbingan::create([
            'nama_anak' => 'Anak ' . $pembimbing->nama_pembimbing,
            'pembimbing_id' => $pembimbing->id,
            'wilayah_id' => $wilayah->id,
            'pelayanan_id' => $pelayanan->id,
        ])->id;
    }
}
