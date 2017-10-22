# PHP SURBTC API
PHP library to handle requests to [SURBTC exchange API](http://api.surbtc.com/).

[SURBTC](https://api.surbtc.com/) is a cryptocurrencies exchange in Chile, Colombia and Peru.

## Installation
1. Clone the repository
2. Include the library in your PHP project

```
require_once 'path/to/repo/Surbtc.php';
```

## Usage

After installing/including the library in your project, you just need to create an instance of the Surbtc class to use its methods.

```
$surbtc = new SurBTC\Surbtc();
$markets = $surbtc->markets();
print_r($markets);
```

Some methods require authentication, you can use your [API keys](http://soporte.surbtc.com/otros-temas/api-de-surbtc) when you create Surbtc objects or using the method authenticate:

```
$surbtc->authenticate('api_key', 'secret_key');
$myBtcBalance = $surbtc->balances('btc');
print_r($myBtcBalance);
```

### Methods

List of methods that don't require authentication:

- ticker($marketId)
- orderBook($marketId)
- trades($marketId, $timestamp = null)
- markets()

List of methods that require authentication:

- balances($currency = null)
- orders($marketId, $per = 300, $page = 1, $state = null, $minimum_exchanged = null)
- createOrder($marketId, $type, $price_type, $limit, $amount)
- cancelOrder($id)
- getOrder($id)
- deposits($currency_code)
- withdrawals($currency_code)

### Markets

IDs of markets available:

- btc-clp
- btc-cop
- eth-clp
- eth-btc
- btc-pen
- eth-pen
- eth-cop

### Currencies

Code of currencies available:

- clp
- cop
- pen
- btc
- eth

### Errors

If a method call response is false, you can check the error code and message using method getLastError:

```
$error = $surbtc->getLastError();
print_r($error);
```

You can compare error code with error codes used by the [SURBTC API](http://api.surbtc.com/#errors).

