<?php

namespace soc;

class OpenAM2020LaravelActions
{


    public static function login()
    {

        $apigeeApiKey = env('AGENTLESS_SSO_KEY');
        $webSSOApi = env('AGENTLESS_SSO_API');
        $cookieName = env("AGENTLESS_SSO_COOKIE_NAME");
        $returnURL = env('AGENTLESS_SSO_RETURN_URL');
        $ssoRedirectURL = env("AGENTLESS_SSO_HEADER_REDIRECT");/**/

        $o = new OpenAM2020($apigeeApiKey, $webSSOApi, $cookieName, $returnURL, $ssoRedirectURL);

        $netid = $o->runAction();


    }
}