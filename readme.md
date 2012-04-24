# PayPal for LaravelPHP #

This package is a simple wrapper for working w/ the [PayPal API](http://coding.smashingmagazine.com/2011/09/05/getting-started-with-the-paypal-api/).

## Install ##

In ``application/bundles.php`` add:

```php
'paypal' => array('auto' => true),
```

Copy the sample config file to ``application/config/paypal.php`` and input the proper information.

## Usage ##

Call the desired method and pass the params as a single array:

```php
$response = Paypal::do_direct_payment(array(
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

Just make sure you pass all the required fields.

## Limitations ##

The package does not handle IPN messages.