<?php

namespace soc;

class OpenAM2020LaravelActions
{


    public static function login()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // if user already logged in, nothing really to do
        // just return
        if (auth()->check()) {
            return;
        }


        $apigeeApiKey = env('AGENTLESS_SSO_KEY');
        $webSSOApi = env('AGENTLESS_SSO_API');
        $ssoRedirectURL = env("AGENTLESS_SSO_HEADER_REDIRECT");
        $DirectoryBasicSearchEndPoint = env('DIRBASIC_ENDPNT');
        $DirectoryBasicSearchEndPointAPIKEY = env('DIRBASIC_APIKEY');

        $o = new OpenAM2020(
            $apigeeApiKey, $webSSOApi, $ssoRedirectURL, $DirectoryBasicSearchEndPoint, $DirectoryBasicSearchEndPointAPIKEY
        );


        // Run actions to get NetID and Email
        try {
            $result = $o->runAction();
            $netid = $result['netid'];
            $email = $result['email'];
            return [
                'netid' => $netid,
                'email' => $email
            ];
        } catch (\Exception $e) {
            dd($e);
        }


    }
}
