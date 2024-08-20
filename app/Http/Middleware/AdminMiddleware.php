<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('admin')->guest()){
            return redirect()->route('admin.login');
        }
        return $next($request);
    }

    //Hardcore Admin middle ware for admin used only
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('admin')->check()) {
            return $this->auth->shouldUse('admin');
        }
        $this->unauthenticated($request, ['admin']);
    }
}
