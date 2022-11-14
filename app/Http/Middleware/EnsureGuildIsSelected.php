<?php

namespace App\Http\Middleware;

use App\Models\wow\Guild;
use Closure;
use Illuminate\Http\Request;

class EnsureGuildIsSelected
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

        $Guild = Guild::session_retrieve();

        if ( !$Guild ){
            return redirect()->route('GuildSelection');
        }
        
        return $next($request);
    }
}
