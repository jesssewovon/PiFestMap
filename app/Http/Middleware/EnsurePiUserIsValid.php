<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Helper\Helper;

use App\Models\PiUser;
use App\Models\PiUsersAccessToken;
use Illuminate\Support\Facades\Log;


class EnsurePiUserIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    	$user = $request->user();
    	$req = PiUser::where('id', $user->id);
    	$user_active = $req->where('active', true)->first();
    	$user_deleted = $req->whereNotNull('deleted_at')->first();
    	if (!is_null($user_deleted)) {
    		return response()->json(['status' => "deleted", 'message' => "Pioneer account deleted, contact admin", 'data' => []], 401);
    	}
        if (is_null($user_active)) {
            return response()->json(['status' => "deactivated", 'message' => "Pioneer account deactivated, contact admin", 'data' => []], 401);
        }
        return $next($request);
    }
}
