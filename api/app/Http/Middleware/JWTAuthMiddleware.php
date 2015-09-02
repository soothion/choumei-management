<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Route;
use App\Exceptions\ApiException;

class JWTAuthMiddleware extends BaseMiddleware
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
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            Throw new ApiException('',-40000);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            Throw new ApiException('',-40001);
        } catch (JWTException $e) {
            Throw new ApiException('',-40000);
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
