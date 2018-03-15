# apitoken
API access token to be used in conjunction with uzzal/acl

## Installation
```
composer require uzzal/apitoken
```

## Configuration
If you are using laravel 5.5+ then this library supports auto discovery. To configure manually 
just edit the `config/app.php` and add service provider like below.
```php
Uzzal\ApiToken\TokenServiceProvider::class
```
At the `app\Http\Kernel.php` add this middleware
```php
'token.checker' => \Uzzal\ApiToken\TokenChecker::class
```

### Database Migration
This library depends on a database table called `auth_tokens`, and it comes with a migration.
So you need to run the migration to add that table with a `artisan` command like
```bash
artisan migrate
```

## Route
Suppose your want to create a api url for the `FaqController` like this `http://YOU-HOST/api/v1/faq` then,
in your `route/api.php` file add your routes like the below

```php
Route::group(['middleware' => ['token.checker'], 'prefix'=>'v1']
    , function(){
    Route::resource('faq', 'FaqController', [
        'only' => ['index']
    ]);
});
``` 
Alternatively, Just in case if you don't have a dedicated `route/api.php` file in that case in your default route file
add the route as below:

```php
Route::group(['middleware' => ['token.checker']
    , 'prefix'=>'api/v1'
    , 'namespace'=> 'Api']
    , function(){
    Route::resource('faq', 'FaqController', [
        'only' => ['index']
    ]);
});
``` 

Now you are all set, but one thing is you need a `_token` to access the protected url.
and you will get the `_token` once you are logged in. So we need a `AuthController` to login for the API.
Here is a sample `AuthController` under the `Api` namespace in the `app/Http/Controllers` directory.

### Controller
```php
<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Uzzal\ApiToken\AuthService;

class AuthController extends Controller {

    protected $auth;
    protected $service;

    public function __construct(Guard $auth, AuthService $service) {
        $this->auth = $auth;
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {        
        $validator = $this->service->validator($request->all());

        if($validator->fails()){
            return ['status'=>'failed', 'msg'=>$validator->messages()];
        }

        $credentials = $request->only('email', 'password');
        if($this->auth->attempt($credentials, $request->has('remember'))){
            return array_merge(['status'=>'success'], $this->service->generateToken());
        }else{
            return ['status'=>'failed','msg'=>'Invalid email or password'];
        }
    }

}
```

__NOTE:__ This Auth controller should be publicly accessable and NOT protected with auth middleware just like this (in  your `route/api.php`)

```php
Route::resource('auth', 'Api\AuthController', [
    'only' => ['store']
]);
```

__How it works:__ If user sends a `POST` request to this `AuthController` with `email` and `password` it will
response with a `_token` (like this `$2y$10$/rUWXPY56sMsyYM6YNfEWea5IPO0xXeETDrAT0SS4dShk24H/fiZ6`) then you can use 
that `_token` to access any protected url like this
`http://YOUR-HOST/api/v1/faq?_token=$2y$10$/rUWXPY56sMsyYM6YNfEWea5IPO0xXeETDrAT0SS4dShk24H/fiZ6`