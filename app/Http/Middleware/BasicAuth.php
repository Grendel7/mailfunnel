<?php


namespace App\Http\Middleware;


use Illuminate\Support\Facades\Log;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $provider The configuration group to get the correct authentication details from
     * @return mixed
     */
    public function handle($request, \Closure $next, $provider)
    {
        if (!empty(config($provider.'.auth'))) {
            if ($request->getUser() !== config($provider.'.auth.username') ||
                $request->getPassword() !== config($provider.'.auth.password')) {

                Log::warning('Invalid authentication from '.$provider, [
                    'username' => $request->getUser(),
                    'password' => $request->getPassword(),
                ]);

                abort(403, "Invalid authentication");
            }
        }

        return $next($request);
    }
}