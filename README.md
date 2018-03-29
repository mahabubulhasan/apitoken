# apitoken
API access token to be used in support with [uzzal/acl](github.com/mahabubulhasan/acl) library

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
Suppose your want to create a api url for the `FaqController` like this `http://YOUR-HOST/api/v1/faq` then,
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

use App\Http\Controllers\Controller;
use Uzzal\ApiToken\Authenticate;

class AuthController extends Controller
{
    use Authenticate;
}
```

__NOTE:__ This Auth controller should be publicly accessable and NOT protected with auth middleware just like this (in  your `route/api.php`)

```php
Route::resource('auth', 'Api\AuthController', [
    'only' => ['store']
]);
```
or
```php
Route::post('auth', 'Api\AuthController@store');
```

__How it works:__ If the user sends a `POST` request to this `AuthController` with `email` and `password` it will
response with a `_token` (like this `$2y$10$/rUWXPY56sMsyYM6YNfEWea5IPO0xXeETDrAT0SS4dShk24H/fiZ6`) then you can use 
that `_token` to access any protected url like this
`http://YOUR-HOST/api/v1/faq?_token=$2y$10$/rUWXPY56sMsyYM6YNfEWea5IPO0xXeETDrAT0SS4dShk24H/fiZ6`

__NOTE:__ You can pass the token via header, and in that case if you are to send the `_token` via header, in that case use `token` __instead of__ *_token* as the header key.