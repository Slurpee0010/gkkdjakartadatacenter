<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'event' => ['nullable', 'string', 'max:80'],
            'module' => ['nullable', 'string', 'max:80'],
            'actor_role' => ['nullable', 'string', 'max:80'],
            'search' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $logs = AuditLog::with('actor')
            ->when($validated['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when($validated['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($validated['actor_role'] ?? null, fn ($query, $role) => $query->where('actor_role', $role))
            ->when($validated['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($validated['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('actor_name', 'like', "%{$search}%")
                        ->orWhere('actor_email', 'like', "%{$search}%")
                        ->orWhere('auditable_label', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit_logs.index', [
            'logs' => $logs,
            'events' => AuditLog::query()->select('event')->distinct()->orderBy('event')->pluck('event'),
            'modules' => AuditLog::query()->select('module')->whereNotNull('module')->distinct()->orderBy('module')->pluck('module'),
            'roles' => AuditLog::query()->select('actor_role')->whereNotNull('actor_role')->distinct()->orderBy('actor_role')->pluck('actor_role'),
        ]);
    }
}
