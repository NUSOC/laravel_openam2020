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
Notes: 
- Use production for basic directory search. It will be more up to date. Development 
directory searches may not provide a valid email address to match the netid. 
- The value of `authIndexValue` determines if MFA will be used. 
- A value of `authIndexValue=ldap-and-duo` will also trigger the method `ensureMFAedConnection(string $token)` to fire. This hits the end session-info endpoint to ask for the boolean value of ` $session_info_data->properties->isDuoAuthenticated`. This prevents someone from changing the value in browser's location bar from `ldap-and-duo` to `ldap-registry` (by passing MFA).

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

First set up middleware:
```
$ php artisan make:middleware SSOMiddleWare
```

Ensure it's connected in Kernal.php 
```
  protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        ...
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        SSOMiddleWare::class
    ];
```


```
 /**
     * This is the piece of middleware that connects Laravel to the
     * OpenAM/SSO module. 
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // assume https
        if (!isset($_SERVER['REQUEST_SCHEME'])) {
            $_SERVER['REQUEST_SCHEME'] = 'https';
        }

        // get netid and email from SSO
        $o2020 = OpenAM2020LaravelActions::login();

        // add user in database
        User::updateOrCreate(
            [
                'name' => $o2020['netid'],
                'email' => $o2020['email']
            ],
            [
                'name' => $o2020['netid'],
                'email' => $o2020['email'],
                'password'=>Str::random(32)     // not ever used
            ]
        );

        // Authenticate into laravel
        auth()->login(User::where('name', $o2020['netid'])->first());

        return $next($request);
    }

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

