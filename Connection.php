<?php
namespace SurBTC;

class Connection{
  /**
   * URL of REST services.
   */
  private $apiBaseUrl = "https://www.surbtc.com/api/v2";

  /**
   * Use them to make private requests.
   */
  private $apiKey;
  private $secretKey;
  
  /**
   * Flag to indicate if debug is enabled.
   */
  public $debug = false;

  const HTTP_OK = [200, 201];
  
  /**
   * Store error making the last request to Bullseye.
   */
  private $lastError = null;

  /**
   * Create a connection to make requests to SurBTC REST API.
   *
   * @param $apiKey string API key to make requests. Default null.
   * @param $secretKey string Secret key to create signatures of requests. Default null.
   */
  function __construct($apiKey = null, $secretKey = null){
    //authenticate
    $this->authenticate($apiKey, $secretKey);
  }
  
  /**
   * Set api key and secret key to make private requests.
   *
   * @param $apiKey string API key to make requests. Default null.
   * @param $secretKey string Secret key to create signatures of requests. Default null.
   */
  function authenticate($apiKey, $secretKey){
    $this->apiKey = $apiKey;
    $this->secretKey = $secretKey;
  }
  
  /**
   * Returns the error in last request made to Bullseye.
   */
  function getLastError(){
    return $this->lastError;
  }

  /**
   * Internal fuction to call the API
   *
   * @param $httpMethod string GET or POST.
   * @param $action string method to call in the API (i.e: markets/btc-cop/ticker.json)
   * @param $args array arguments to send in the request. Arguments are transformed to a query string or included in POST data based on HTTP method. Default empty array.
   * @param $private boolean if true, then authentication headers are included in the request. Default false.
   */
  public function query($httpMethod, $action, $args = array(), $private = false) {
    //build URL of web service
    $fullUrl = $this->apiBaseUrl . '/' . $action;

    //check if request is GET
    $httpMethod = strtolower($httpMethod);
    if( "get" == $httpMethod && !empty($args)) {
      $fullUrl .= '?'. http_build_query($args);
    }

    //build CURL object
    $curl = curl_init();
    $options = array();
    $bodyString = "";
    $options[CURLOPT_URL] = $fullUrl;
    $options[CURLOPT_RETURNTRANSFER] = true;
    $options[CURLOPT_HTTPHEADER] = [
      'Content-Type: application/json'
    ];
    $options[CURLINFO_HEADER_OUT] = true;


    //check if request is POST
    if(in_array($httpMethod, ['post', 'put'])) {
      $bodyString = json_encode($args);

      $options[CURLOPT_POST] = 1;
      $options[CURLOPT_CUSTOMREQUEST] = strtoupper($httpMethod);
      $options[CURLOPT_POSTFIELDS] = $bodyString;
      $options[CURLOPT_HTTPHEADER] []= 'Content-Length: ' . strlen($bodyString);
    }
    
    //add authentication requests
    if($private)
      $options[CURLOPT_HTTPHEADER] = array_merge(
        $options[CURLOPT_HTTPHEADER],
        $this->createAuthenticationHeaders($httpMethod, $fullUrl, $bodyString)
      );

    //execute request
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    //if debug is enabled
    if($this->debug){
      echo "Curl Info: ";
      print_r(curl_getinfo($curl));
      
      if(in_array($httpMethod, ['post', 'put'])) {
        echo "Post Data: ";
        print_r($args);
      }
      
      echo "Response: ";
      print_r($response);
    }

    //get HTTP response code for the request
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    //validate response
    if ($err) {
      //store error making the request
      $this->lastError = array(
        'code' => $httpcode,
        'response' => $err
      );
    
      return array($httpcode, $err);
    }
    
    //store error in request response
    if(!in_array($httpcode, self::HTTP_OK))
      $this->lastError = array(
        'code' => $httpcode,
        'response' => json_decode($response, true)
      );
    else
      $this->lastError = null;
    
    //returns response
    return array($httpcode, json_decode($response, true));
  }

  /**
   * Create authentication headers to include in private requests.
   *
   * @param $httpMethod string GET or POST.
   * @param $url string Full url of the request.
   * @param $body string Body to send in request. Default empty.
   *
   * @return array Headers to include in request.
   */
  private function createAuthenticationHeaders($httpMethod, $url, $body = ""){
    //get nonce
    $nonce = time() . rand(10000, 99999);
    
    //get URL path
    $urlPath = parse_url($url, PHP_URL_PATH);
    //add query parameters
    if(false !== strpos($url, '?'))
     $urlPath .= '?' . parse_url($url, PHP_URL_QUERY);

    //encode body with base64
    $encodedBody = base64_encode($body);
    if($this->debug)
      echo "Body: {$body}\n";

    //get string to sign
    $signatureBase = sprintf("%s %s %s %s", strtoupper($httpMethod), $urlPath, $encodedBody, $nonce);
    //remove extra space if there is no body
    $signatureBase = str_replace('  ', ' ', $signatureBase);
    if($this->debug)
      echo "Signature base: {$signatureBase}\n";

    //create signature
    $signature = hash_hmac('sha384', $signatureBase, $this->secretKey, false);

    return [
      "X-SBTC-APIKEY: {$this->apiKey}",
      "X-SBTC-NONCE: {$nonce}",
      "X-SBTC-SIGNATURE: {$signature}"
    ];
  }
  
  /**
   * This function makes a query and process its response. It is created to not duplicate code in most of requests.
   *
   * @param $method array Associative array with info of method to execute. i.e:
   *   'GetCatSum' => [
   *     'httpMethod' => 'get',
   *     'action' => 'path/to/action.json',
   *   ]
   * @param $requestData array Associative array with data to send in request.
   * @param $private boolean if true, then authentication headers are included in the request. Default false.
   *
   * @return mixed false if there is an error. Otherwise the request response.
   */
  public function request($method, $requestData = array(), $private = false){
    //extract arg to make query
    $httpMethod = $action = null;
    extract($method);
    
    //makes requests
    list($httpcode, $response) = $this->query($httpMethod, $action, $requestData, $private);
    
    //validate HTTP code of response
    if(!in_array($httpcode, self::HTTP_OK))
      return false;
    
    //returns response
    return $response;
  }
}

