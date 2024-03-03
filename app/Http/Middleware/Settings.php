<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Setting;

class Settings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    	$on_maintenance = Setting::where('name', 'maintenance_mode')->first();
    	if (!is_null($on_maintenance) && $on_maintenance->value == 1) {
    		return response()->json([
    			'status' => "maintenance_mode",
    			'message' => "Maintenance mode is on",
    			'data' => [],
    		], 401);
    	}
        return $next($request);
    }
}
