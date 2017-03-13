<?php

namespace Wzulfikar\CheckRollbarLimit;

use Illuminate\Console\Command;

class CheckRollbarLimitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rollbar:check-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check rollbar free plan monthly limit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * The `RollbarLimitExceeded` event will 
     * be triggered only if rollbar limit is exceeded.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->check(config('services.rollbar.access_token'));
        if (!empty($response['is_over_limit'])) {
        	// default to `rollbar`
        	$tokenName = 'rollbar';
    
        	// check if system already recorded rollbar_access_token
        	if ($accessToken = \Cache::get('rollbar_access_token')) {
        		if (!empty($accessToken['token_name'])) {
	        		$tokenName = $accessToken['token_name'];
        		}
        	}
	        event(new \Wzulfikar\CheckRollbarLimit\RollbarLimitExceededEvent($tokenName));
        }
    }

    private function check($access_token, $word = 'over free plan', $env = 'production')
    {
    	$payload = [
    	  "access_token" => $access_token,
    	  "data" => [
    	    "environment" => $env,
    	    "body" => [
    	      "message" => [
    	        "body" => "Test rate limit"
    	      ]
    	    ]
    	  ]
    	];

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL,"https://api.rollbar.com/api/1/item/");
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	$response = curl_exec ($ch);
    	curl_close ($ch);

    	$jsonResponse = json_decode($response, true);

    	if (empty($jsonResponse['err']) && empty($jsonResponse['message'])) {
    		return $jsonResponse;
    	}

    	if (stripos($jsonResponse['message'], $word) !== false) {
    		$jsonResponse['is_over_limit'] = true;
    	}

    	return $jsonResponse;
    }
}
