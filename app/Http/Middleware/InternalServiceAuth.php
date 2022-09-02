<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InternalServiceAuth
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header("Authorization");
        try {
            $token = explode(" ", $authorizationHeader)[1];
            if ($token == config("internal_service.token")) {
                return $next($request);
            }
        } catch (Exception $exception) {
        }
        return response()->json(
            ["success" => false, "message" => "Access denied"],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
