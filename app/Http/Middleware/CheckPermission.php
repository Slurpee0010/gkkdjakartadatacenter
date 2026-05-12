<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'read'): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        $action = $action === 'auto' ? $this->inferAction($request) : $action;

        if (! $user->hasPermissionTo($module, $action)) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki izin untuk mengakses modul ini.');
        }

        return $next($request);
    }

    private function inferAction(Request $request): string
    {
        $methodName = $request->route()?->getActionMethod();

        return match ($methodName) {
            'create', 'store' => 'create',
            'edit', 'update' => 'update',
            'approve' => 'approve',
            'reject' => 'reject',
            'destroy' => 'delete',
            'index', 'show', 'report', 'export', 'exportCsv', 'exportExcel', 'exportIndex',
            'getPembimbing', 'getAnakPa', 'getBlesscomnByFilter',
            'previewNamaIbadah', 'summaryWeeklyApi', 'averageAttendanceApi' => 'read',
            default => match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'read',
            },
        };
    }
}
