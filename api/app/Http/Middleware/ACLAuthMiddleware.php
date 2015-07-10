<?php namespace App\Http\Middleware;

use Closure;
use Route;
use JWTAuth;
use App\Exceptions\NoAuthException;

class ACLauthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        $user = JWTAuth::parseToken()->authenticate();

        
        $permission = Route::currentRouteName();
        if(!$user->can($permission))
            throw new NoAuthException("unauthorized",402);
            
        return $next($request);
    }
}
