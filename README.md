# laravel_openam2020


## Note
This is still a work in progress. 

## Set Environment Variables, 
Ensure your base project has the following in the following in the `.env` file. 

```
AGENTLESS_SSO_KEY="o....0"
AGENTLESS_SSO_API="https://server-test.apigee.net/agentless-websso/validateWebSSOToken"
AGENTLESS_SSO_COOKIE_NAME="op....en"
AGENTLESS_SSO_RETURN_URL="htt....00"
AGENTLESS_SSO_HEADER_REDIRECT="Location: https://...du/nusso/XUI/?....d-duo&goto="
```

## Set up Repo
In the composer file, add
```
"repositories": [
        {
            "url": "https://github.com/NUSOC/laravel_openam2020.git",
            "type": "git"
        }
    ],
```

## In console
```
composer require soc/openam2020
```

## In a controller
```
public function index() {
    OpenAM2020LaravelActions::login();
    return true;
}
```


## Other
This is a project based off https://github.com/NIT-Administrative-Systems/Agentless-WebSSO to help me understand how this procedure works. 

