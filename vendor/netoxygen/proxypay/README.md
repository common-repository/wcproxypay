[![Latest Stable Version](https://poser.pugx.org/netoxygen/proxypay/v/stable)](https://packagist.org/packages/netoxygen/proxypay)
[![Build Status](https://travis-ci.org/NetOxygen/proxypay.svg?branch=master)](https://travis-ci.org/NetOxygen/proxypay)
[![Coverage Status](https://coveralls.io/repos/github/NetOxygen/proxypay/badge.svg?branch=master)](https://coveralls.io/github/NetOxygen/proxypay?branch=master)

# ProxyPay library

Provides methods to compose SHA signatures and to check [ProyPay](https://proxypay.ch/) responses.

Full documentation can be found [here](doc/doc.md)

## Requirements:

- PHP 5.5+

## Installation:

The library is [PSR-4 compliant](http://www.php-fig.org/psr/psr-4)
and the simplest way to install it is via composer:

     composer require netoxygen/proxypay

## Description
### Request and Responses signature
All the communication between your server and ProxyPay is signed using **HMAC SHA256**.

### Keys
You have been provided two keys:

 - **key_in**: use it to sign your requests
 - **key_out**: use it to check ProxyPay responses signature

## Usage
### Signing your requests to ProxyPay
You have to add in your request a param named **sha_sign** (or SHA_SIGN).
The easier way to sign your requests is to use the `PaymentRequest` class.

```php
use Netoxygen\ProxyPay\PaymentRequest;

/**
  * We assume that $_POST contains all your request parameters:
  * - seller
  * - amount
  * - description
  * - success_url
  * - error_url
  * - cancel_url
  *
  * All the other parameters will be filtered.
  */
$request = new PaymentRequest($_POST, 'my_key_in');

// You can loop over all your parameters in the $request
foreach ($request->get_parameters() as $key => $value) {
    echo $key . '=' . $value;
}

// You can retrieve the SHA_SIGN
$sha_sign = $request->compose_sha_sign();
```

### Check ProxyPay responses signature
At the end of the process, you receive a signed GET callback from ProxyPay.
To ensure that the request is correctly signed, just use the `Paymentresponse` class.

```php
use Netoxygen\ProxyPay\PaymentResponse;

$response = new PaymentResponse($_GET, 'my_key_out');
if ($response->is_valid()) {
    // $response signature is verified, now check the transaction status
    $params         = $response->get_parameters();
    $payment_status = $params['STATUS'];
    switch($payment_status) {
        case 'SUCCESS':
            // Complete the transaction on your side
            break;
  
        case 'CANCEL':
            // The transaction has been cancelled by the user
            break;
  
        case 'ERROR':   /* FALLTHROUGH */
        default:
            // An error occured
            break;
} else {
    // Bad request: throw away
}
```
