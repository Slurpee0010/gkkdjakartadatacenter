<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NotificationInboxController extends Controller
{
    public function index(Request $request): View
    {
        $roleName = $request->user()->role?->name;

        $notifications = AppNotification::with(['sender.role', 'targetWilayah'])
            ->where(function ($query) use ($request, $roleName) {
                $query->where('target_user_id', $request->user()->id)
                    ->orWhereJsonContains('target_roles', $roleName);
            })
            ->latest('sent_at')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function create(Request $request): View
    {
        return view('notifications.create', [
            'allowedRoles' => $this->allowedRoles($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();
        $allowedRoles = $this->allowedRoles($request);

        if ($allowedRoles === []) {
            throw ValidationException::withMessages([
                'target_roles' => 'Akun ini tidak dapat mengirim broadcast.',
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'target_roles' => ['nullable', 'array'],
            'target_roles.*' => ['required', Rule::in(array_keys($allowedRoles))],
        ]);

        $targetRoles = $validated['target_roles'] ?? array_keys($allowedRoles);
        $targetRoles = array_values(array_intersect($targetRoles, array_keys($allowedRoles)));

        if ($targetRoles === []) {
            throw ValidationException::withMessages([
                'target_roles' => 'Pilih minimal satu role target.',
            ]);
        }

        AppNotification::create([
            'sender_id' => $actor->id,
            'target_roles' => $targetRoles,
            'title' => strip_tags(trim($validated['title'])),
            'message' => strip_tags(trim($validated['message'])),
            'sent_at' => now(),
            'metadata' => [
                'channel' => 'broadcast',
                'source' => 'web_inbox',
            ],
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Broadcast berhasil dikirim ke inbox role target.');
    }

    private function allowedRoles(Request $request): array
    {
        $allowedRoleNames = config('rbac.notification_target_roles.' . ($request->user()->role?->name ?? ''), []);

        return collect(config('rbac.roles'))
            ->only($allowedRoleNames)
            ->except(Role::USER)
            ->all();
    }
}
