<?php

/**
 * A LaravelPHP package for working w/ PayPal.
 *
 * @package    PayPal
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-paypal
 * @license    MIT License
 */

class Paypal
{
	public static function __callStatic($method, $args)
	{
		// if production mode...
		if (Config::get('paypal.production_mode') === true)
		{
			// use production credentials
			$credentials = Config::get('paypal.production');
			
			// use production endpoint
			$endpoint = 'https://api-3t.paypal.com/nvp';
		}
		
		// if sandbox mode...
		else 
		{
			// use sandbox credentials
			$credentials = Config::get('paypal.sandbox');
			
			// use sandbox endpoint
			$endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		}

		// build credentials
		$params = array(
			'VERSION' => '74.0',
			'USER' => $credentials['username'],
			'PWD' => $credentials['password'],
			'SIGNATURE' => $credentials['signature'],
			'METHOD' => static::camelcase($method),
		);
		
		// build post data
		$fields = http_build_query($params + $args[0]);
		
		// curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);

		// if errors...
		if (curl_errno($ch))
		{
			#$errors = curl_error($ch);
			curl_close($ch);
			
			// return false
			return false;
		}
		
		// if NO errors...
		else
		{
			curl_close($ch);
			
			// return array
			parse_str($response, $result);
			return $result;
		}
	}
	
	public static function ipn()
	{
		// only accept post data
		if (Request::method() !== 'POST') return false;
	
		// if production mode...
		if (Config::get('paypal.production_mode'))
		{
			// use production endpoint
			$endpoint = 'https://www.paypal.com/cgi-bin/webscr';
		}
		
		// if sandbox mode...
		else 
		{
			// use sandbox endpoint
			$endpoint = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		
		// build response
		$fields = http_build_query(array('cmd' => '_notify-validate') + Input::all());
		
		// curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		// if errors...
		if (curl_errno($ch))
		{
			#$errors = curl_error($ch);
			curl_close($ch);
			
			// return false
			return false;
		}
		
		// if NO errors...
		else
		{
			curl_close($ch);
			
			// if success...
			if ($code === 200 and $response === 'VERIFIED')
			{
				return true;
			}
			
			// if NOT success...
			else
			{
				return false;
			}
		}
	}
	
	protected static function camelcase($str)
	{
		return ucfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($str)));
	}
}
