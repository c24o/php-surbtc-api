<?php
//1. Include Surbtc library
require_once '../Surbtc.php';

//2. Create Surbtc object
$surbtc = new SurBTC\Surbtc();

//2.1 activate debug mode
//$surbtc->debug(true);

//2.2 authenticate if you don't authenticate in constructor
$surbtc->authenticate('api_key', 'secret_key');

//3. Call method to get an order
$response = $surbtc->getOrder(4039845);

//4. Check response
if(false != $response)
  print_r($response);
else{
  print_r($surbtc->getLastError());
}

