<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()->symbols()],
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['new_password']),
        ])->save();

        $auditLogger->log(AuditLog::EVENT_PASSWORD_CHANGED, [
            'module' => 'users',
            'auditable_type' => $user::class,
            'auditable_id' => $user->id,
            'auditable_label' => $user->email,
            'metadata' => [
                'target_user_id' => $user->id,
                'target_email' => $user->email,
                'target_role' => $user->role?->name,
                'changed_by_self' => true,
                'password_value' => '[redacted]',
            ],
        ]);

        return redirect()->route('password.edit')
            ->with('success', 'Password berhasil diganti.');
    }
}
