<?php
/**
 * PHP interface to connect to SurBTC API.
 */
namespace SurBTC;

require_once 'Connection.php';

class Surbtc {
  /**
   * Connection to the API
   */
  private $connection;

  /**
   * Create a connection to make requests to SurBTC REST API.
   *
   * @param $apiKey string API key to make requests. Default null.
   * @param $secretKey string Secret key to create signatures of requests. Default null.
   */
  function __construct($apiKey = null, $secretKey = null){
    //create connection to the API
    $this->connection = new Connection($apiKey, $secretKey);
  }

  /**
   * Enable or disable debug messages in requests.
   *
   * @param $activate boolean if true, then debug messages are activated. Otherwise, debug messages are disabled.
   */
  function debug($activate = true){
    $this->connection->debug = $activate;
  }

  /**
   * Returns the error in last request made to SurBTC.
   * @see http://api.surbtc.com/#errors
   *
   * @return mixed if there was an error in last request made, then an array with error info is returned. Otherwise null is returned.
   */
  function getLastError(){
    return $this->connection->getLastError();
  }

  /**
   * Set api key and secret key to make private requests.
   *
   * @param $apiKey string API key to make requests. Default null.
   * @param $secretKey string Secret key to create signatures of requests. Default null.
   */
  function authenticate($apiKey, $secretKey){
    $this->connection->authenticate($apiKey, $secretKey);
  }

  /** *************************************
   * Public Requests
   ** ************************************/
  
  /**
   * @see http://api.surbtc.com/#ticker
   *
   * @param $marketId string ID of market
   */
  function ticker($marketId){
    $method = array(
      'httpMethod' => 'get',
      'action' => "markets/{$marketId}/ticker",
    );

    return $this->connection->request($method);
  }

  /**
   * @see http://api.surbtc.com/#order-book
   *
   * @param $marketId string ID of market
   */
  function orderBook($marketId){
    $method = array(
      'httpMethod' => 'get',
      'action' => "markets/{$marketId}/order_book",
    );

    return $this->connection->request($method);
  }

  /**
   * @see http://api.surbtc.com/#trades
   *
   * @param $marketId string ID of market
   * @param $timestamp int timestamp of first trade to request with microseconds. Default null.
   */
  function trades($marketId, $timestamp = null){
    $method = array(
      'httpMethod' => 'get',
      'action' => "markets/{$marketId}/trades",
    );
    
    return $this->connection->request($method, compact('timestamp'));
  }

  /**
   * @see http://api.surbtc.com/#markets
   */
  function markets(){
    $method = array(
      'httpMethod' => 'get',
      'action' => "markets",
    );
    
    return $this->connection->request($method);
  }

  /** *************************************
   * Private Requests
   ** ************************************/
  
  /**
   * @see http://api.surbtc.com/#balances
   *
   * @param $currency string currency symbol. Default null.
   */
  function balances($currency = null){
    $method = array(
      'httpMethod' => 'get',
      'action' => "balances/{$currency}",
    );

    return $this->connection->request($method, null, true);
  }

  /**
   * @see http://api.surbtc.com/#mis-rdenes
   *
   * @param $marketId string ID of market
   * @param $per integer Numbers of orders to retrieve. Max value is 300. Default value is 300.
   * @param $page integer Number of page to retrieve(1-indexed). Default is 1.
   * @param $state string State of orders to retrieve. Default null.
   * @param $minimum_exchanged float Minimum value in the order. Default is null.
   */
  function orders($marketId, $per = 300, $page = 1, $state = null, $minimum_exchanged = null){
    $method = array(
      'httpMethod' => 'get',
      'action' => "markets/{$marketId}/orders",
    );

    return $this->connection->request($method, compact('per', 'page', 'state', 'minimum_exchanged'), true);
  }

  /**
   * @see http://api.surbtc.com/#nueva-orden
   *
   * @param $marketId string ID of market
   * @param $type string Direction of the order, buy or sell(Bid|Ask).
   * @param $price_type string Type of order(limit|market).
   * @param $limit float Price of the order.
   * @param $amount float Amount to exchange.
   */
  function createOrder($marketId, $type, $price_type, $limit, $amount){
    $method = array(
      'httpMethod' => 'post',
      'action' => "markets/{$marketId}/orders",
    );

    return $this->connection->request($method, compact('type', 'price_type', 'limit', 'amount'), true);
  }

  /**
   * @see http://api.surbtc.com/#cancelar-orden
   *
   * @param $id string ID of the order to cancel.
   */
  function cancelOrder($id){
    $method = array(
      'httpMethod' => 'put',
      'action' => "orders/{$id}",
    );

    return $this->connection->request($method, ['state' => 'canceling'], true);
  }

  /**
   * @see http://api.surbtc.com/#estado-de-la-orden
   *
   * @param $id string ID of the order to retrieve.
   */
  function getOrder($id){
    $method = array(
      'httpMethod' => 'get',
      'action' => "orders/{$id}",
    );

    return $this->connection->request($method, null, true);
  }

  /**
   * @see http://api.surbtc.com/#historial-de-depositos-retiros
   *
   * @param $currency_code string Code of currency.
   */
  function deposits($currency_code){
    $method = array(
      'httpMethod' => 'get',
      'action' => "currencies/{$currency_code}/deposits",
    );

    return $this->connection->request($method, null, true);
  }

  /**
   * @see http://api.surbtc.com/#historial-de-depositos-retiros
   *
   * @param $currency_code string Code of currency.
   */
  function withdrawals($currency_code){
    $method = array(
      'httpMethod' => 'get',
      'action' => "currencies/{$currency_code}/withdrawals",
    );

    return $this->connection->request($method, null, true);
  }

  /**
   * @see http://api.surbtc.com/#nuevo-retiro
   */
  function createWithdrawal(){
    echo "Not supported yet.";
    return false;
  }

  /**
   * @see http://api.surbtc.com/#dep-sito-dinero-fiat
   */
  function createFiatDeposit(){
    echo "Not supported yet.";
    return false;
  }

  /**
   * @see http://api.surbtc.com/#dep-sito-criptomonedas
   */
   function createCryptoDeposit(){
    echo "Not supported yet.";
    return false;
  }
}
