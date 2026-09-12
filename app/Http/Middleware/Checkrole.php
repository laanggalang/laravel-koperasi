<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 2. Ambil role user saat ini
        $userRole = auth()->user()->role;

        // 3. Loloskan jika user adalah 'admin' (Super Admin bebas akses apapun)
        if ($userRole === 'admin') {
            return $next($request);
        }

        // 4. Periksa apakah role user terdaftar di parameter rute
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 5. Jika tidak cocok, blokir dengan error 403
        abort(403, 'Maaf, Anda tidak memiliki hak akses untuk membuka halaman ini.');
    }
}