<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;

class Mailgun
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $type
     * @return mixed
     */
    public function handle($request, \Closure $next, $type)
    {
        $string = $request->get('timestamp') . $request->get('token');
        $signature = hash_hmac('sha256', $string, config('mailgun.webhook_key.' . $type));

        if ($signature != $request->get('signature')) {
            Log::warning('Invalid Mailgun signature', [
                'received' => $request->get('signature'),
                'expected' => $signature,
            ]);
            abort(403, "Invalid Mailgun signature");
        }

        return $next($request);
    }
}
