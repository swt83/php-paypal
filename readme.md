# PayPal

A PHP package for working w/ the Paypal API.

## Install

Normal install via Composer.

## Usage

Call the desired method and pass the params as a single array.  Be sure to include a ``$credentials`` array as the first argument.

```php

$credentials = array(
    'username' => '',
    'password' => '',
    'signature' => '',
    'sandbox' => true // optional
);

$response = Travis\Paypal::do_direct_payment($credentials, array(
    // ip address
    'IPADDRESS' => Request::ip(),

    // credit card
    'CREDITCARDTYPE' => '',
    'ACCT' => '',
    'EXPDATE' => '',
    'CVV2' => '',

    // name
    'FIRSTNAME' => '',
    'LASTNAME' => '',

    // email
    'EMAIL' => '',

    // address
    'COUNTRYCODE' => 'US',
    'STREET' => '',
    'CITY' => '',
    'STATE' => '',
    'ZIP' => '',

    // payment
    'INVNUM' => '',
    'AMT' => 100,
    'DESC' => '',
));
```

Read the [PayPal API](http://coding.smashingmagazine.com/2011/09/05/getting-started-with-the-paypal-api/) docs for a list of available methods.

## Listener

You can accept and verify IPN communications.  Just setup a route where you'll receive the IPN post data and run the ``ipn()`` method.  Use ``ipn(array('sandbox' => true))`` for testing in sandbox mode.

```php
Route::post('ipn', function() {

    // if data verifies...
    if (Paypal::ipn()) // method returns true or false, success or failure
    {
        // capture data
        $data = Input::all();

        // do something w/ data
        // ...
    }

});
```