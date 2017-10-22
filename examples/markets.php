<?php
//1. Include Surbtc library
require_once '../Surbtc.php';

//2. Create Surbtc object
$surbtc = new SurBTC\Surbtc();

//2.1 activate debug mode
//$surbtc->debug(true);

//3. Call method to get markets
$response = $surbtc->markets();

//4. Check response
if(false != $response)
  print_r($response);
else{
  print_r($surbtc->getLastError());
}

