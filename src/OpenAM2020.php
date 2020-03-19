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
    protected $DirectoryBasicSearchEndPoint;
    protected $DirectoryBasicSearchEndPointAPIKEY;


    /**
     * OpenAM2020 constructor.
     * @param $apigeeApiKey
     * @param $webSSOApi
     * @param $cookieName
     * @param $returnURL
     * @param $ssoRedirectURL
     * @param $requiresMFA
     */
    public function __construct($apigeeApiKey, $webSSOApi, $cookieName, $returnURL, $ssoRedirectURL, $requiresMFA = true, $DirectoryBasicSearchEndPoint, $DirectoryBasicSearchEndPointAPIKEY)
    {
        $this->apigeeApiKey = $apigeeApiKey;
        $this->webSSOApi = $webSSOApi;
        $this->cookieName = $cookieName;
        $this->returnURL = $returnURL;
        $this->ssoRedirectURL = $ssoRedirectURL;
        $this->requiresMFA = $requiresMFA;
        $this->DirectoryBasicSearchEndPoint = $DirectoryBasicSearchEndPoint;
        $this->DirectoryBasicSearchEndPointAPIKEY = $DirectoryBasicSearchEndPointAPIKEY;
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

        $result = $this->getIsSessionValid($token);

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

        dump([
            $result,
           // $context
        ]);
        return $result['netid'];
    }

    /**
     * @param $token
     * @return resource
     * Is the session valid?
     */
    public function getIsSessionValid($token)
    {

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
                'ignore_errors' => false,
            ],
        ]);

        return file_get_contents($this->webSSOApi, false, $context);
    }


    /**
     * @param string $netid
     * @return resource
     *
     * Gets email address via netid. Using same pattern as before
     * with stream context to keep some consistency.
     *
     */
    public function getEmailAddressFromNetid(string $netid)
    {

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    "Content-Length: 0",
                    "apikey: " . $this->DirectoryBasicSearchEndPointAPIKEY,
                ]),
                'ignore_errors' => false,
            ],
        ]);
        return file_get_contents($this->DirectoryBasicSearchEndPoint, false, $context);
    }



}

