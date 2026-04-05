<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRoles = auth()->user()->roles->pluck('name')
            ->map(fn($n) => strtolower($n));

        if ($userRoles->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda belum memiliki role. Hubungi administrator.'], 403);
            }
            return redirect()->route('access.status', ['reason' => 'no_role']);
        }

        foreach ($roles as $role) {
            if ($userRoles->contains(strtolower($role))) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke halaman ini.'], 403);
        }

        return redirect()->route('access.status', ['reason' => 'area_mismatch']);
    }
}
