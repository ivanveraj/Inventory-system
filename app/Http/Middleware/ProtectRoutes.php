<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProtectRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request):((Response|RedirectResponse)) $next
     * @return Response|RedirectResponse
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
