<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if($request->user()->role !== $role ){
            // Redirect to appropriate dashboard based on user's actual role
            $userRole = $request->user()->role;
            
            if($userRole === 'admin'){
                return redirect('/admin/dashboard');
            } elseif($userRole === 'agent'){
                return redirect('/agent/dashboard');
            } else {
                return redirect('/dashboard');
            }
        }
        return $next($request);
    }
}
