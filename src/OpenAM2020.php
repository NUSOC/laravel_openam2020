<?php

namespace soc;

class OpenAM2020
{


    protected $apigeeApiKey;
    protected $webSSOApi;
    protected $cookieName;
    protected $returnURL;
    protected $ssoRedirectURL;
    protected $requiresMFA;


    /**
     * OpenAM2020 constructor.
     * @param $apigeeApiKey
     * @param $webSSOApi
     * @param $cookieName
     * @param $returnURL
     * @param $ssoRedirectURL
     * @param $requiresMFA
     */
    public function __construct($apigeeApiKey, $webSSOApi, $cookieName, $returnURL, $ssoRedirectURL, $requiresMFA = true)
    {
        $this->apigeeApiKey = $apigeeApiKey;
        $this->webSSOApi = $webSSOApi;
        $this->cookieName = $cookieName;
        $this->returnURL = $returnURL;
        $this->ssoRedirectURL = $ssoRedirectURL;
        $this->requiresMFA = $requiresMFA;
    }


    /**
     * Send the user to the online passport login page.
     */
     public function redirectToLogin()
    {
        $redirect = urlencode($this->returnURL . '/' . $_SERVER['REQUEST_URI']);

        header($this->ssoRedirectURL . $redirect);
        exit;
    }

    /**
     * Get the value of a cookie, if it exists.
     * @param $name
     * @return mixed|null
     */
     public function getCookieValue($name)
    {
        $token = null;
        if (array_key_exists($name, $_COOKIE) == true) {
            $token = $_COOKIE[$name];
        }

        return $token;
    }

    public function runAction()
    {
        // Do we have a session?
        $token = $this->getCookieValue('nusso');
        if ($token == null) {
            $this->redirectToLogin();
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
                    "apikey: " . $this->apigeeApiKey,
                    "webssotoken: $token",
                    "requiresMFA: " . $this->requiresMFA,
                    "goto: ", // not using this functionality
                ]),
                'ignore_errors' => true,
            ],
        ]);

        $result = file_get_contents($this->webSSOApi, false, $context);
        if ($result === false) {
            $this->redirectToLogin();
        }

        $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (array_key_exists('fault', $result)) {
            echo "Your apigee key is not valid:<br><pre>";
            print_r($result);
            echo "</pre>";
            die();
        }

        if (array_key_exists('netid', $result) === false) {
            $this->redirectToLogin();
        }

        // dd($result);
        return $result['netid'];
    }

}

