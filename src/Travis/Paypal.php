<?php

namespace Travis;

class Paypal {

    /**
     * Magic method for handling API methods.
     *
     * @param   string  $method
     * @param   array   $args
     * @return  array
     */
    public static function __callStatic($method, $args)
    {
        // capture arguments
        $credentials = isset($args) ? $args[0] : array();
        $args = isset($args) ? $args[1] : array();

        // catch error...
        if (!isset($credentials))
        {
            trigger_error('No credentials provided.');
        }

        // set endpoint
        $endpoint = 'https://api-3t.paypal.com/nvp';
        if (isset($credentials['sandbox']))
        {
            if ($credentials['sandbox'])
            {
                $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            }
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
        $fields = http_build_query($params + $args);

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
            // capture
            #$errors = curl_error($ch);

            // close
            curl_close($ch);

            // return
            return false;
        }

        // if NO errors...
        else
        {
            // close
            curl_close($ch);

            // parse
            parse_str($response, $result);

            // return
            return $result;
        }
    }

    /**
     * Automatically verify Paypal IPN communications.
     *
     * @param   array   $args
     * @return  boolean
     */
    public static function ipn($args = array())
    {
        // only accept post data
        if (\Request::method() !== 'POST') return false;

        // set endpoint
        $endpoint = 'https://www.paypal.com/cgi-bin/webscr';
        if (isset($args['sandbox']))
        {
            if ($args['sandbox'])
            {
                $endpoint = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            }
        }

        // build response
        $fields = http_build_query(array('cmd' => '_notify-validate') + \Input::all());

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
            // capture
            #$errors = curl_error($ch);

            // close
            curl_close($ch);

            // return
            return false;
        }

        // if NO errors...
        else
        {
            // close
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

    /**
     * Convert method names to camelcase.
     *
     * @param   string  $str
     * @return  string
     */
    protected static function camelcase($str)
    {
        return ucfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($str)));
    }

}