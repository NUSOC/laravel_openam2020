<?php

namespace soc;

class OpenAM2020
{

    static function test()
    {
        return 'Test message.';
    }


    static function getConfiguration()
    {
        return [
            'apigeeApiKey' => env('AGENTLESS_SSO_KEY'),
            'webSSOApi' => env('AGENTLESS_SSO_API'),
            'cookieName' => env("AGENTLESS_SSO_COOKIE_NAME"),
            'returnURL'=> env('AGENTLESS_SSO_RETURN_URL'),
        ];
    }

    /**
     * Send the user to the online passport login page.
     */
    static function redirectToLogin()
    {
        $redirect = urlencode(self::getConfiguration()['returnURL'] . '/' . $_SERVER['REQUEST_URI']);

        header(env("AGENTLESS_SSO_HEADER_REDIRECT") . $redirect);
        exit;
    }

    /**
     * Get the value of a cookie, if it exists.
     */
    static function getCookieValue($name)
    {
        $token = null;
        if (array_key_exists($name, $_COOKIE) == true) {
            $token = $_COOKIE[$name];
        }

        return $token;
    }

    static function runAction()
    {
        // Do we have a session?
        $token = self::getCookieValue('nusso');
        if ($token == null) {
            self::redirectToLogin();
        }

        /*
        * Is the session valid?
        *
        * You can use any HTTP library you want here.
        * I'm using stream_context_create since it's a PHP built-in,
        * but I recommend using Guzzle instead!
        */
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    "Content-Length: 0",
                    "apikey: $apigeeApiKey",
                    "webssotoken: $token",
                    "requiresMFA: true",
                    "goto: ", // not using this functionality
                ]),
                'ignore_errors' => true,
            ],
        ]);

        $result = file_get_contents($webSSOApi, false, $context);
        if ($result === false) {
            self::redirectToLogin();
        }

        $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (array_key_exists('fault', $result)) {
            echo "Your apigee key is not valid:<br><pre>";
            print_r($result);
            echo "</pre>";
            die();
        }

        if (array_key_exists('netid', $result) === false) {
            self::redirectToLogin();
        }

        dd($result);
        //        $netid = $result['netid'];
    }

}

