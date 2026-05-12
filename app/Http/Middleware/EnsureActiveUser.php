<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        if (! $user->isActive()) {
            $message = $user->status === User::STATUS_PENDING_DELETION
                ? 'Akun sedang menunggu persetujuan penghapusan.'
                : 'Akun tidak aktif.';

            abort(Response::HTTP_FORBIDDEN, $message);
        }

        return $next($request);
    }
}
