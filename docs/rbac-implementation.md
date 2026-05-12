# RBAC, Regional Data Scope, dan Approval Workflow

Dokumen ini mencatat kontrak implementasi RBAC untuk aplikasi Laravel ini.

## Skema Database

Tabel inti:

- `roles`: `superadmin`, `admin_pusat`, `admin_wilayah`, `user`.
- `permissions`: kombinasi `module` + `action`, termasuk wildcard `*.*`.
- `permission_role`: pivot many-to-many role dan permission.
- `users`: ditambah `role_id`, `wilayah_id`, `status`, `deletion_requested_by`, `deletion_requested_at`, `deleted_at`.
- `user_deletion_requests`: antrean approval hapus user dari admin pusat ke superadmin.
- `app_notifications`: outbox notifikasi broadcast/filter role.
- `audit_logs`: audit trail login, created, updated, deleted, dan password_changed.
- `wilayahs`: master wilayah; `users.wilayah_id` nullable untuk role yang tidak dibatasi wilayah.

Status user:

- `active`: akun aktif.
- `pending_deletion`: admin pusat meminta hapus, menunggu superadmin.
- `deleted`: superadmin menyetujui atau superadmin menghapus langsung; record user terkena soft delete.

Status approval:

- `pending`
- `approved`
- `rejected`

## Matrix Role

`superadmin`

- Permission wildcard `*.*`.
- CRUD user dan role target `admin_pusat`, `admin_wilayah`, `user`.
- Kirim notifikasi ke semua role.
- Approve/reject `user_deletion_requests`.

`admin_pusat`

- CRUD `blesscomn`, `pa`, `kehadiran_ibadah`.
- Tidak diberi permission `master_data.*`.
- CRUD user terbatas untuk role `admin_wilayah` dan `user`.
- Delete user tidak menghapus data, tetapi membuat `user_deletion_requests` dan mengubah user menjadi `pending_deletion`.
- Kirim notifikasi hanya ke `admin_wilayah` dan `user`.

`admin_wilayah`

- CRUD `blesscomn`, `pa`, `kehadiran_ibadah`.
- Semua query otomatis dibatasi `wilayah_id` akun.
- Semua write mengabaikan input wilayah dari client dan memakai wilayah akun.

`user`

- Tidak perlu login.
- Hanya boleh POST ke endpoint publik `laporan-pa` dan `blesscomn`.
- Tidak ada endpoint GET publik untuk role ini.

## Middleware dan Service

Middleware:

- `active.user`: menolak akun yang bukan `active`.
- `permission:{module},{action}`: validasi permission role, termasuk wildcard.
- `permission:{module},auto`: memetakan method controller/HTTP ke `read`, `create`, `update`, atau `delete`.
- `regional.scope:{module},{field}`: inject wilayah untuk admin wilayah.

Service:

- `App\Services\Rbac\DataScope`
  - `applyToRequestQuery($query, $request, $column)`: menambahkan `WHERE {column} = user.wilayah_id`.
  - `injectRegionIntoRequest($request, $field)`: override field wilayah saat POST/PUT/PATCH.
  - `wilayahOptionsFor($user)`: untuk frontend, admin wilayah hanya menerima satu opsi wilayah.
- `App\Services\Rbac\UserDeletionApprovalService`
  - `requestDeletion()`
  - `approve()`
  - `reject()`

## API

Endpoint publik:

- `POST /api/public/laporan-pa`
- `POST /api/public/blesscomn`

Keduanya memakai `throttle:public-submissions`, validasi ketat, validasi relasi lintas field, dan kalkulasi total di backend.

Endpoint admin:

- `GET /api/admin/me`
- `GET /api/admin/users`
- `POST /api/admin/users`
- `PUT/PATCH /api/admin/users/{user}`
- `DELETE /api/admin/users/{user}`
- `GET /api/admin/roles`
- `POST /api/admin/roles`
- `PUT /api/admin/roles/{role}`
- `DELETE /api/admin/roles/{role}`
- `GET /api/admin/user-deletion-requests`
- `POST /api/admin/user-deletion-requests/{request}/approve`
- `POST /api/admin/user-deletion-requests/{request}/reject`
- `POST /api/admin/notifications`

Protected API memakai middleware `auth`, `active.user`, dan `permission`.

Endpoint web juga memakai session auth:

- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /password`
- `PUT /password`
- `GET /audit-logs`

Route web modul dikunci dengan `auth`, `active.user`, dan `permission:{module},auto`.

## Audit Log

Audit log dibuat oleh `App\Observers\AuditLogObserver` untuk model utama dan oleh controller auth/password untuk event khusus:

- `login`
- `created`
- `updated`
- `deleted`
- `password_changed`

Nilai password tidak pernah disimpan di `audit_logs`. Event `password_changed` menyimpan metadata aman seperti target user, role, IP address, dan user agent, dengan nilai password selalu `[redacted]`.

Hanya superadmin yang dapat membuka halaman `GET /audit-logs`.

## Contoh Query Scoping

Eloquent:

```php
$query = LaporanPa::with(['wilayah', 'pelayanan']);
app(DataScope::class)->applyToRequestQuery($query, $request, 'wilayah_id');
```

Query Builder:

```php
$query = DB::table('kehadiran_ibadah')
    ->whereNull('kehadiran_ibadah.deleted_at');

app(DataScope::class)->applyToRequestQuery($query, $request, 'kehadiran_ibadah.id_wilayah');
```

Write payload:

```php
app(DataScope::class)->injectRegionIntoRequest($request, 'id_wilayah');
$validated = $request->validate([...]);
```

## Frontend

Gunakan data dari `GET /api/admin/me`:

- `data.role.name === "admin_wilayah"` berarti field wilayah harus read-only/disabled.
- Isi value wilayah dengan `data.wilayah.id`.
- Tetap kirim hidden input wilayah jika memakai `<select disabled>`, karena field disabled tidak ikut submit.
- Jangan percaya hidden input untuk keamanan. Backend tetap melakukan override wilayah.

Blade pattern:

```blade
@php($regionalLocked = auth()->user()?->isAdminWilayah())

@if($regionalLocked)
    <input type="hidden" name="id_wilayah" value="{{ auth()->user()->wilayah_id }}">
@endif

<select name="id_wilayah" id="id_wilayah" class="gkkd-form-control" @disabled($regionalLocked)>
    @foreach ($wilayahs as $wilayah)
        <option value="{{ $wilayah->id }}" @selected(old('id_wilayah', auth()->user()?->wilayah_id) == $wilayah->id)>
            {{ $wilayah->nama_wilayah }}
        </option>
    @endforeach
</select>
```
