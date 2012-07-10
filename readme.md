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

Read the [PayPal API](http://coding.smashingmagazine.com/2011/09/05/getting-started-with-the-paypal-api/) docs for what kind of response objects to except.

## Listener ##

You can accept and verify IPN communications.  Just setup a route where you'll receive the IPN post data and run the ``ipn()`` method:

```
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

Keeping it simple.