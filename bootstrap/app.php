<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 🚀 استثناء مسارات الكاميرا من حماية CSRF عشان تشتغل طول اليوم بدون تقطيع
        $middleware->validateCsrfTokens(except: [
            'face-login',
            'save-student-attendance',
            '/face-login',
            '/save-student-attendance'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();