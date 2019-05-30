<?php
namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthentication
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
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {
             return response()->json( [
                'status' => false,
                'code'  => 11,
                'message' => 'Token Expired'
            ]);

        } catch (TokenInvalidException $e) {
             return response()->json( [
                'status' => false,
                'code'  => 12,
                'message' => 'Invalid Token'
            ]);

        } catch (JWTException $e) {
             return response()->json( [
                'status' => false,
                'code'  => 13,
                'message' => 'Token absent'
            ]);

        }
        return $next($request);
    }
}