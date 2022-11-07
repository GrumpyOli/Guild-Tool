<?php

namespace App\Http\Middleware;

use App\Blizzard\Connection\Token;
use Closure;
use Illuminate\Http\Request;

class EnsureBnetAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $Token = Token::retrieve();

        if ( !$Token || $Token->isExpired() ){
            return redirect()->route('LoginPage');
        }

        return $next($request);
        
    }
}
