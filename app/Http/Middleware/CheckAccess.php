<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccess
{
    /**
     * Usage: middleware('access:lihat.fakultas')
     *        middleware('access:tambah.fakultas,edit.fakultas')  // any of these
     */
    public function handle(Request $request, Closure $next, string ...$accesses): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->deny($request);
        }

        $userAccesses = $user->allAccesses();

        foreach ($accesses as $access) {
            if ($userAccesses->contains($access)) {
                return $next($request);
            }
        }

        return $this->deny($request);
    }

    private function deny(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk tindakan ini.'], 403);
        }

        return redirect()->route('access.status', ['reason' => 'forbidden']);
    }
}
