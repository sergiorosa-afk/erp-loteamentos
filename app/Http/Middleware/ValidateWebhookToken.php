<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateWebhookToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token    = $request->header('X-Webhook-Token');
        $expected = config('services.webhook.secret');

        if (! $token || ! $expected || ! hash_equals($expected, $token)) {
            return response()->json(['erro' => 'Token inválido ou ausente'], 401);
        }

        return $next($request);
    }
}
