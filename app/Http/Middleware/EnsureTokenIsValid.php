<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\PiUsersAccessToken;
use App\Traits\AuthTrait;

use Illuminate\Support\Facades\Log;

class EnsureTokenIsValid
{
	use AuthTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    	$customAccessToken = $request->header('Authorization');
    	Log::info('$customAccessToken : '.$customAccessToken);
    	if (!$customAccessToken) {
    		return response()->json([
                'status' => "no_custom_token",
                'message' => "No custom token",
                'data' => []
            ], 422);
    	}
    	$req = PiUsersAccessToken::where('custom_token', $customAccessToken)->where('active', true)->where('expired', false);
        $tokenObject = $req->first();
    	//Log::info('$piAccessToken : '.$tokenObject->token);
    	if (!$tokenObject || !$tokenObject->token) {
    		return response()->json([
                'status' => "no_pi_token",
                'message' => "No Pi token",
                'data' => []
            ], 403);
    	}
        $tokenObject_not_expired = $req->where('expires_at', '>', now()->format('Y-m-d H:i:s'))->first();
        if (!$tokenObject_not_expired) {
            $req->where('expires_at', '<=', now()->format('Y-m-d H:i:s'))->update(['expired' => 1]);
            return response()->json([
                'status' => "token_expired",
                'message' => "Token expired",
                'data' => []
            ], 401);
        }
    	if (!$this->isPiTokenValid($tokenObject->token)) {
            $req->update(['expired' => 1]);
    		return response()->json(['message' => "unauthorised request", 'data' => []], 401);
    	}
        return $next($request);
    }
}
