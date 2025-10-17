<?php

namespace App\Http\Middleware;

use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Models\User;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

use Closure;
use Exception;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        try {
            $user     = JWTAuth::parseToken()->authenticate();
            $userData = User::select('id', 'name', 'email')->find($user->id);

            $request->setUserResolver(function () use ($userData) {
               return $userData;
            });

        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return HttpHandler::errorResponse(Constants::INVALID_TOKEN, JsonResponse::HTTP_FORBIDDEN);
            } else if ($e instanceof TokenExpiredException) {
                return HttpHandler::errorResponse(Constants::EXPIRED_TOKEN, JsonResponse::HTTP_FORBIDDEN);
            }

            return HttpHandler::errorResponse(Constants::TOKEN_NOT_FOUND, JsonResponse::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
