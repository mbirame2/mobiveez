<?php

namespace App\Http\Middleware;

use Closure;

class particulier
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
       // var_dump(auth('api')->user()->isRole());die();
        if(auth('api')->user() && auth('api')->user()->isRole()=='particulier'){
           return $next($request);
        }else{
            return redirect()->route('error');
        }
       
    }
}
