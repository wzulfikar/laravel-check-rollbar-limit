<?php 

namespace Wzulfikar\CheckRollbarLimit;

trait InitRollbarTrait { 
  /**
   * Reconfigure rollbar access token
   * @return void
   */
  private function initRollbar()
  {
  	$rollbar_access_token = \Cache::get('rollbar_access_token', config('services.rollbar.access_token'));
	\Rollbar::init($rollbar_access_token);
  }
}