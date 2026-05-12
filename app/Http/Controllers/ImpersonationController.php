<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function store(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $superadmin = $request->user();

        abort_unless($superadmin?->isSuperadmin(), 403, 'Hanya superadmin yang dapat impersonate user.');
        abort_if($superadmin->id === $user->id, 422, 'Tidak perlu impersonate akun sendiri.');
        abort_if($user->hasRole(Role::SUPERADMIN), 422, 'Akun superadmin tidak dapat di-impersonate.');
        abort_if($user->hasRole(Role::USER), 422, 'Role user adalah guest publik dan tidak memiliki tampilan login.');
        abort_if(! $user->isActive(), 422, 'Hanya user aktif yang dapat di-impersonate.');

        $auditLogger->log(AuditLog::EVENT_IMPERSONATION_STARTED, [
            'actor' => $superadmin,
            'module' => 'users',
            'auditable_type' => $user::class,
            'auditable_id' => $user->id,
            'auditable_label' => $user->email,
            'metadata' => [
                'target_user_id' => $user->id,
                'target_email' => $user->email,
                'target_role' => $user->role?->name,
            ],
        ]);

        $request->session()->put('impersonator_id', $superadmin->id);
        $request->session()->put('impersonator_name', $superadmin->name);
        $request->session()->put('impersonator_email', $superadmin->email);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route($user->hasPermissionTo('pa', 'read') ? 'dashboard' : 'password.edit')
            ->with('success', 'Mode impersonate aktif untuk ' . $user->email . '.');
    }

    public function destroy(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $impersonatedUser = $request->user();
        $impersonatorId = $request->session()->pull('impersonator_id');
        $request->session()->forget(['impersonator_name', 'impersonator_email']);

        abort_if(! $impersonatorId, 403, 'Sesi impersonate tidak aktif.');

        $superadmin = User::withTrashed()->findOrFail($impersonatorId);

        Auth::login($superadmin);
        $request->session()->regenerate();

        $auditLogger->log(AuditLog::EVENT_IMPERSONATION_STOPPED, [
            'actor' => $superadmin,
            'module' => 'users',
            'auditable_type' => $impersonatedUser ? $impersonatedUser::class : null,
            'auditable_id' => $impersonatedUser?->id,
            'auditable_label' => $impersonatedUser?->email,
            'metadata' => [
                'target_user_id' => $impersonatedUser?->id,
                'target_email' => $impersonatedUser?->email,
                'target_role' => $impersonatedUser?->role?->name,
            ],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Mode impersonate dihentikan.');
    }
}
