<?php namespace App\Http\Middleware;

use Closure;
use Route;
use JWTAuth;
use Input;

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

        $param = Input::all();
        $user = JWTAuth::parseToken()->authenticate();
        
        $permission = Route::currentRouteName();

        //用户操作自己帐户信息
        if((substr($permission, 0,5)=='user.')&&isset($param['id'])&&($user->id==$param['id']))
            return $next($request);

        if(!$user->can($permission))
            throw new \Exception("unauthorized",402);
            
        return $next($request);
    }
}
