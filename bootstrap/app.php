<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\Checkrole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // GET /logout: redirect sesuai status login (route logout hanya menerima POST)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('logout')) {
                return redirect()
                    ->route(auth()->check() ? 'dashboard' : 'login')
                    ->with('error', 'Aksi logout harus dikirim melalui tombol Keluar.');
            }

            return null;
        });

        // CSRF token mismatch (419): arahkan user ke halaman yang ramah, bukan halaman error
        // Catatan: Laravel 11 mengubah TokenMismatchException menjadi HttpException(419) sebelum render callbacks
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi telah berakhir. Silakan muat ulang halaman dan coba lagi.',
                ], 419);
            }

            return redirect()
                ->route(auth()->check() ? 'dashboard' : 'login')
                ->with('error', 'Sesi Anda telah berakhir demi keamanan. Silakan coba lagi.');
        });
    })->create();
