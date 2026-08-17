<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya kirim HSTS kalau request-nya memang HTTPS
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        $response->headers->set('Content-Security-Policy', $this->cspPolicy());

        return $response;
    }

    private function cspPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            // cdn.tailwindcss.com -> Tailwind Play CDN yang dipakai di layout salesman/admin
            // cdnjs.cloudflare.com -> Alpine.js
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com cdnjs.cloudflare.com",
            // cdnjs.cloudflare.com juga ditambahkan di style-src karena Tailwind Play CDN
            // menyuntikkan <style> lewat JS yang sebagian di-load dari domain itu
            "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdnjs.cloudflare.com",
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}