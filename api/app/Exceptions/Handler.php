<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Response;
use JWTAuth;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // return parent::render($request, $e);
        $token = '';
        $code = 0;
        if(JWTAuth::getToken()){
            try {
                $token = JWTAuth::parseToken()->refresh();
            } 
            catch (Exception $e){
                //
            }
            
        }
        if(method_exists($e,'getCode'))
            $code = $e->getCode();
        if(method_exists($e,'getStatusCode'))
            $code = $e->getStatusCode();
        return Response::json(['result'=>0,'code'=>$code,'token'=>$token,'msg'=>$e->getMessage()]);
    }
}
