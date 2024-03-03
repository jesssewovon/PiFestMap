<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait AuthTrait{
	public function isPiTokenValid($token)
	{
        try {
        	$client_http = new \GuzzleHttp\Client(["verify"=>false]);
            $rep = $client_http->request('GET', 'https://api.minepi.com/v2/me', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ],
            ]);
            $statusCode = $rep->getStatusCode();
            if ($statusCode != 200) {
            	Log::info('false');
                return false;
            }
            Log::info('true');
            return true;
        } catch (\Exception $e) {
            return false;
        }
	}
}