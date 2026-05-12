<?php

namespace App\Http\Middleware;

use App\Services\Rbac\DataScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectRegionalScope
{
    public function __construct(private readonly DataScope $dataScope)
    {
    }

    public function handle(Request $request, Closure $next, ?string $module = null, string ...$fields): Response
    {
        $user = $request->user();
        $wilayahId = $this->dataScope->scopedWilayahId($user);

        if ($wilayahId === null) {
            return $next($request);
        }

        abort_if(! $user?->wilayah_id, Response::HTTP_FORBIDDEN, 'Admin wilayah belum memiliki wilayah.');

        $request->attributes->set('scope.wilayah_id', $wilayahId);

        $fields = $fields ?: [config("rbac.regional_scope_columns.{$module}", 'wilayah_id')];

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            foreach ($fields as $field) {
                $request->merge([$field => $wilayahId]);
            }
        }

        return $next($request);
    }
}
