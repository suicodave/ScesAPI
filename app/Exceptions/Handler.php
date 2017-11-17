<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof AuthorizationException){
            if($request->wantsJson()){
                return response()->json([
                    "externalMessage" => "This user is not permitted to authorize this action.",
                    "internalMessage" => "Unauthorized action"
                ],401);
            }
            return redirect()->route("unauthorized");

        }

        if($exception instanceof MethodNotAllowedHttpException ){
            if($request->wantsJson()){
                return response()->json([
                    "externalMessage" => "The HTTP method used is not available for the requested data.",
                    "internalMessage" => "Http method not allowed for this route."
                ],404);
            }
            
        }

        if($exception instanceof ModelNotFoundException ){
            return response()->json([
                "externalMessage" => "There are no results for the given data.",
                "internalMessage" => "Resource Not Found!"
            ],404);
        }

        if($exception instanceof QueryException){
            return response()->json([
                "externalMessage" => "There are no results for the given data.",
                "internalMessage" => $exception->errorInfo
            ],404);
        }

        

        return parent::render($request, $exception);
        
       
        
    }
}
