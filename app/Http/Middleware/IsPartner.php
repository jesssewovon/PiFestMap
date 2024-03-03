<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;
use App\Traits\Helper;
use App\Models\PiUser;

class IsPartner
{
    use Helper;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        //$id_user = $request->user_id;
    	if (!$user || !$user->is_partner || is_null($user->password)) {
            Log::info('not partner');
    		return response()->json([
                'status' => false,
                'message' => 'not a partner',
    		], 403);
    	}
        return $next($request);
    }
}
