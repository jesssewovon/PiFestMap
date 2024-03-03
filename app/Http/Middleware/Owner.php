<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;
use App\Traits\Helper;
use App\Models\PiUser;

class Owner
{
    use Helper;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_uid = $request->header('useruid');
        //$id_user = $request->user_id;
    	if ($user_uid == '' || $request->user()->uid != $user_uid) {
            Log::info('not owner');
    		return response()->json([
    		], 403);
    	}
        return $next($request);
    }
}
