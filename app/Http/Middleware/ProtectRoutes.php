<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProtectRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        $array = explode('|', $permissions);
        for ($i = 0; $i < count($array); $i++) {
            if (validatePermission(Auth::user(), $array[$i])) {
                return $next($request);
            }
        }
        return redirect()->route('dashboard');
    }

}
