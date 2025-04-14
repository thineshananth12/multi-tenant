<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (TokenExpiredException $e, $request) {
            return response()->json(['error' => 'Token has expired'], 401);
        });
    
        $this->renderable(function (TokenInvalidException $e, $request) {
            return response()->json(['error' => 'Token is invalid'], 401);
        });
    
        $this->renderable(function (JWTException $e, $request) {
            return response()->json(['error' => 'Token is missing'], 401);
        });
    
        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            return response()->json(['error' => 'Unauthorized'], 401);
        });
    
        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        });
    }

    

    
}
