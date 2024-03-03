<?php

namespace App\PiSDK;

use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;

use Illuminate\Support\Facades\Log;

class PiNetwork
{
    private $api_key = "vgufdu7tprbpdsrjxybfpkf6eci8q7crg1kbbcmends5vtulnotn5jv1gjpzsfzj";
    private $seed = 'SA42DNL3KE3DBTDQ4MH4KWMUEIFEWZR3ZVQ6CR226BJJV2W5KQX72ODL';
    private $keypair;

    public function __construct(){
    	//$this->api_key = $api_key;
    	//$this->keypair = Keypair::newFromSeed($this->seed);//SA42DNL3KE3DBTDQ4MH4KWMUEIFEWZR3ZVQ6CR226BJJV2W5KQX72ODL
    }

    public function createPayment($paymentData)
    {
    	$rr = ['payment'=>$paymentData];
    	$rr = json_encode($paymentData);
    	//https://api.testnet.minepi.com
    	//https://api.minepi.com
    	$body = '';
    	$client_http = new \GuzzleHttp\Client(["verify"=>false]);
    	try {
    		$rep = $client_http->request('POST', 'https://api.mainnet.minepi.com/v2/payments', [
	            'headers' => [
	                'Accept' => 'application/json',
	                'Authorization' => 'Key '.$this->api_key
	            ],
	            'query' => $rr,
	        ]);
	        $body = $rep->getBody();
    	} catch (\Exception $e) {
    		Log::info('MESSAGE '.$e->getMessage());
    	}
        
        //Log::info("[body] $body");
        $body_obj = json_decode($body, false, 512, JSON_UNESCAPED_UNICODE);
        return $body_obj;
    }
}
