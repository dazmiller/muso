<?php

namespace App\Http\Middleware;

use Closure;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (file_exists(base_path('storage/app/installed.lock'))) {
            return response()->json([
                "success" => false,
                "message" => 'This endpoint doesn\'t exist.',
            ], 404);
        }

        return $next($request);
    }
}
