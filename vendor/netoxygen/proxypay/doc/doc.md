# Proxypay Payment Platform
*V. 1.8.1 - 2020-07-17*

## Integration documentation
### Overview

The Proxypay Payment Platform (PPP) by Net Oxygen allows you to receive payments through other Payment Providers (Postfinance, Credit Cards, Twint) without having an account with them.

### Global Workflow
 - 1 - Get the user data on your website
 - 2 - Generate the form to send to the PPP
 - 3 - The PPP redirects the user to Postfinance
 - 4 - The user makes the payment
 - 5 - Postfinance redirects the user to the PPP
 - 6 - The PPP redirects the user to your website

### PPP Form
**URL**
The form must be submitted with the <strong>POST</strong> method to <strong>https://payment.proxypay.ch/payment/form</strong><br/>

**Mandatory fields**

| Name              | Description                                                                                   | Example                                                             |
|-----------------  |---------------------------------------------------------------------------------------------- |-------------------------------------------------------------------- |
| **amount**        | The TTC amount of the transaction as a decimal.                                               | *12.50*                                                             |
| **description**   | The transaction description, as a 100\* max characters string.                                  | *Don spontané*                                                      |
| **seller**        | The unique seller id you received from Net Oxygen.                                            | *my_seller_name*                                                    |
| **success_url**   | The absolute URL where to redirect the user on sucess, as a 200\* max characters string.        | *https://www.your_website.tld/success?id=155*                       |
| **error_url**     | The absolute URL where to redirect the user on error, as a 200\* max characters string.         | *https://www.your_website.tld/error?id=155*                         |
| **cancel_url**    | The absolute URL where to redirect the user on cancellation, as a 200\* max characters string.  | *https://www.your_website.tld/cancel?id=155*                        |
| **sha_sign**      | The SHA256 signature of the query params (see Requests and Responses signature)               | *5440b26de45174f4a51a35a4acb209ad2fc83fac71a801bfc0afa53b6ad2a002*  |
\*: *Longer values will be truncated*

**Optional fields**

The optional fields are about the buyer and the language to use for Postfinance

| Name                | Description                                                                                                                      | Example                |
|---------------------|----------------------------------------------------------------------------------------------------------------------------------|------------------------|
| **fullname**        | Buyer fullname, as a 35\* max characters string.                                                                                  | *Belkacem Alidra*      |
| **email**           | Buyer email address, as a 50\* max characters string.                                                                              | *support@netoxygen.ch* |
| **address**         | Buyer address, as a 35\* max characters string.                                                                                    | *Avenue d'aïre 56*     |
| **zip**             | Buyer postal code, as an 10\* max characters string.                                                                               | *1203*                 |
| **city**            | Buyer city, as a 25\* max characters string.                                                                                       | *Genève*               |
| **country**         | Buyer country code, as an <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">ISO 3166-1 alpha-2</a> 2 characters string. | *CH*                   |
| **lang**            | Postfinance interface language. Possible values: fr_FR (default), ar_AR, cs_CZ, dk_DK, de_DE, el_GR, es_ES, fi_FI, fr_FR, he_IL, hu_, it_IT, ja_JP, ko_KR, nl_BE, nl_NL, no_NO, pl_PL, pt_PT, ru_RU, se_SE, sk_SK, tr_TR, zh_CN | *en_US*                |
| **callback_url**    | The absolute URL where to redirect if the user leave the page before to be redirected to the success_url                          | *https://www.your_website.tld/callback?id=155* |
| **callback_method** | The method which callback_url must be called. Possible values: POST, GET                                                          | *POST*                                         |
\*: *Longer values will be truncated*


**Test Mode**

By default, the test mode is disabled: all transactions are for real.

You can enable it by sending in your form a field named **test** with the value **1**
Sending 0 or omitting the field has the same effect

| Name     | Description                                         |
|----------|-----------------------------------------------------|
| test     | Enable the test mode with the value 1, 0 to disable |


### Postfinance page customization
You can choose, on the Postfinance payment page, the background color and the top left logo.<br>

 - To change the background color (white by default), send us the html code: ex: #FF0000 for red.<br/>
 - To change the top left logo, send us your logo  in png format.

### Email notifications
You can provide us one email address which will receive a notification on each transaction, after the transaction has completed (notification for a succesfull/cancelled/error transaction).

### Request and Responses signature
All the communication between your server and the PPP is signed using HMAC SHA256.

**Keys**

You have been provided two keys:

 - **key_in**: to sign your requests
 - **key_out**: to check our responses signature

**Net Oxygen Payment PHP library**

The library is [PSR-4 compliant](http://www.php-fig.org/psr/psr-4)
and the simplest way to install it is via composer:

```
composer require netoxygen/proxypay
```

Require the Composer autoloader if it's not already done.

It can also be found on [Github](https://github.com/NetOxygen/proxypay)

**Signing your requests**

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

### Form example
    <form method="POST" action="https://payment.proxypay.ch/payment/form" class="form-horizontal">
        <input type="hidden" name="seller" value="my_seller_name" />
        <input type="hidden" name="amount" value="12.50" />
        <input type="hidden" name="description" value="Don spontané" />

        <!-- Enable test mode: remove the field to be in production mode -->
        <input type="hidden" name="test" value="1" />

        <input type="hidden" name="success_url"
                value="https://www.your_website.tld/success/" />
        <input type="hidden" name="error_url"
                value="https://www.your_website.tld/error/" />
        <input type="hidden" name="cancel_url"
                value="https://www.your_website.tld/cancel/" />
        <input type="hidden" name="callback_url"
                value="https://www.your_website.tld/callback/" />
        <input type="hidden" name="callback_method"
                value="POST" />
        <input type="hidden" name="sha_sign"
                value="5440b26de45174f4a51a35a4acb209ad2fc83fac71a801bfc0afa53b6ad2a002" />

        <div class="form-group">
            <label for="fullname" class="col-sm-2 control-label">Nom complet</label>
            <div class="col-sm-10"> <input type="text" class="form-control" name="fullname" placeholder="Nom complet" /></div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10"> <input type="email" class="form-control" name="email" placeholder="Email" /> </div>
        </div>
        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">Adresse</label>
            <div class="col-sm-10"> <input type="text" class="form-control" name="address" placeholder="Adresse" /> </div>
        </div>
        <div class="form-group">
            <label for="zip" class="col-sm-2 control-label">Code postal</label>
            <div class="col-sm-10"> <input type="text" class="form-control" name="zip" placeholder="Code postal" /> </div>
        </div>
        <div class="form-group">
            <label for="city" class="col-sm-2 control-label">Ville</label>
            <div class="col-sm-10"> <input type="text" class="form-control" name="city" placeholder="Cille" /> </div>
        </div>
        <div class="form-group">
            <label for="country" class="col-sm-2 control-label">Pays</label>
            <div class="col-sm-10"> <input type="text" class="form-control" name="country" placeholder="Pays" /> </div>
        </div>
    </form>
