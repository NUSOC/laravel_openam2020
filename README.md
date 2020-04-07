# laravel_openam2020


## Note
This is still a work in progress. 

## Set Environment Variables, 
Ensure your base project has the following in the following in the `.env` file. 

```
AGENTLESS_SSO_KEY="o....0"
AGENTLESS_SSO_API="https://server-test.apigee.net/agentless-websso/validateWebSSOToken"
AGENTLESS_SSO_HEADER_REDIRECT="Location: https://...du/nusso/XUI/?....d-duo&goto="
DIRBASIC_ENDPNT=https://server-prod.apigee.net/directory-search/res/netid/bas/
DIRBASIC_APIKEY=nlM....
```
Note: Use production for basic directory search. It will be more up to date. Development 
directory searches may not provide a valid email address to match the netid. 

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

## In middleware
```
// returns [
//   'netid'=>"abc123",
//   'email'=>"account@domain.ext"
// ]
$o2020 = OpenAM2020LaravelActions::login();
auth()->login(User::where('netid', $o2020['netid'])->first());

```


## To set up development environment
Start with a new laravel project, add the following to your composer file. Change folder location to match where this plugin folder is located. 

```
    "repositories": {
        "local": {
            "type":"path",
            "url": "$HOME/folder/openam_2020_module"
        }
    }
```

and of 

## Other
This is a project based off https://github.com/NIT-Administrative-Systems/Agentless-WebSSO to help me understand how this procedure works. 

