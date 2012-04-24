<?php

/**
 * A LaravelPHP package for working with PayPal.
 *
 * @package    Paypal
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-paypal
 * @license    MIT License
 */

class Paypal
{
	public static function __callStatic($method, $args)
	{
		// load config
		$config = Config::get('paypal');
		if ($config['sandbox_mode'] === true)
		{
			$credentials = $config['sandbox'];
			$endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		}
		else 
		{
			$credentials = $config['production'];
			$endpoint = 'https://api-3t.paypal.com/nvp';
		}

		// set credentials
		$params = array(
			'VERSION' => '74.0',
			'USER' => $credentials['username'],
			'PWD' => $credentials['password'],
			'SIGNATURE' => $credentials['signature'],
			'METHOD' => self::camelize($method),
		);
		
		// build post data
		$fields = http_build_query($params + $args[0]);
		
		// setup curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);

		// catch errors
		if (curl_errno($ch))
		{
			#$errors = curl_error($ch);
			curl_close($ch);
			
			// return false
			return false;
		}
		else
		{
			curl_close($ch);
			
			// return array
			parse_str($response, $result);
			return $result;
		}
	}
	
	private static function camelize($str)
	{
		return ucfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($str)));
	}
}