<?php

namespace App\Http\Middleware;

use App\Logs;
use Closure;

class LogTransfer
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
        $response = $next($request);
        Logs::create(['request' => $request,'response' => $response]);
        return $response;
    }
}
