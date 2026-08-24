<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAusbildungInfoIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      $user = $request->user();
      
      if ($user && !$user->ausbildung_info_completed_at
        && !$request->routeIs('ausbildung-info.*')
        && !$request->routeIs('logout')) {
        return redirect(route('ausbildung-info.create'));
      }
      
      return $next($request);
    }
}
