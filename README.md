# apitoken
API access token to be used in conjunction with uzzal/acl

## Installation
```
composer require uzzal/apitoken
```

## Confiuration
At the `config/app.php` add service provider
```php
Uzzal\ApiToken\TokenServiceProvider::class
```
At the `app\Http\Kernel.php` add this middleware
```php
'token.checker' => \Uzzal\ApiToken\TokenChecker::class
```


## Route

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


## Controller
```php
<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Uzzal\ApiToken\AuthService;
use Log;

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
        //Todo remove loggins
        Log::debug('Log In Data: '.  json_encode($request->all()));
        Log::info('User Agent: '.$_SERVER['HTTP_USER_AGENT']);
        Log::info('Remote Address: '.$_SERVER['REMOTE_ADDR']);
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
