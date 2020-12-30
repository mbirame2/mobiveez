<?php

namespace App\Http\Middleware;

use Closure;

class professionnel
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
        if(auth('api')->user() && auth('api')->user()->isRole()=='professionnel'){
            return $next($request);
         }else{
             return redirect()->route('error');
         }
    }
}
