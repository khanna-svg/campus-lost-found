<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
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
