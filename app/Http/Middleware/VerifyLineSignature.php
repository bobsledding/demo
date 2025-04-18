<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyLineSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Line-Signature');
        $body = $request->getContent();
        $secret = config('services.line.channel_secret');

        if (!$signature || !$secret) {
            abort(400, 'Missing signature or channel secret.');
        }

        $hash = base64_encode(hash_hmac('sha256', $body, $secret, true));

        if (!hash_equals($hash, $signature)) {
            abort(403, 'Invalid signature.');
        }

        return $next($request);
    }
}
