<?php

namespace soc;

use soc\OpenAM2020;

class OpenAM2020LaravelActions
{


    public static function login()
    {

        $apigeeApiKey = env('AGENTLESS_SSO_KEY');
        $webSSOApi = env('AGENTLESS_SSO_API');
        $cookieName = env("AGENTLESS_SSO_COOKIE_NAME");
        $returnURL = env('AGENTLESS_SSO_RETURN_URL');
        $ssoRedirectURL = env("AGENTLESS_SSO_HEADER_REDIRECT");/**/

        $o = new \soc\OpenAM2020t($apigeeApiKey, $webSSOApi, $cookieName, $returnURL, $ssoRedirectURL);


    }
}