<?php

use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Vercel (and most hosts) terminate HTTPS at a proxy; trust it so
        // Laravel generates https:// links and secure cookies correctly.
        $middleware->trustProxies(at: '*');

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role' => EnsureRole::class,
            'active' => EnsureUserIsActive::class,
            'password.changed' => EnsurePasswordIsChanged::class,
        ]);

        // Already logged-in visitors of /login are sent to their own home page.
        $middleware->redirectUsersTo(fn (Request $request) => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

/*
|--------------------------------------------------------------------------
| Serverless (Vercel) storage path
|--------------------------------------------------------------------------
|
| On Vercel the project directory is READ-ONLY; only /tmp is writable (and it
| is not shared between function instances). Laravel still needs a writable
| storage folder for compiled views and temporary files, so we point it at /tmp.
| Nothing important may live there: sessions/cache are in the database and
| uploads go to Supabase Storage.
|
*/
if (getenv('VERCEL') || getenv('APP_SERVERLESS') === 'true') {
    $storage = '/tmp/storage';

    foreach (['app', 'framework/cache', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
        if (! is_dir("{$storage}/{$dir}")) {
            @mkdir("{$storage}/{$dir}", 0755, true);
        }
    }

    $app->useStoragePath($storage);
}

return $app;
