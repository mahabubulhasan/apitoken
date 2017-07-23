<?php
namespace Uzzal\ApiToken;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


/**
 *
 * @author Mahabubul Hasan <codehasan@gmail.com>
 */
class AuthService
{
    /**
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($data) {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }

    private static $_token_user;

    public static function isValidToken($token) {
        if (!self::$_token_user) {
            self::$_token_user = AuthToken::where('token', '=', $token);
        }

        if (self::$_token_user->exists()) {
            if(!Auth::check()){
                Auth::loginUsingId(self::$_token_user->first()->user_id);
            }
            return true;
        }

        return false;
    }

    public static function logout() {
        $user_id = self::$_token_user->first()->user_id;
        Auth::logout();
        return AuthToken::where('user_id', '=', $user_id)->delete();
    }

    /**
     *
     * @return array|boolean
     */
    public function generateToken() {
        $user = Auth::user();
        $user_id = $user->user_id;
        $name = $user->name;
        $email = $user->email;

        $token = bcrypt($user_id . date('l Y-m-d H:i:s') . rand(1, 9999));
        if (AuthToken::create([
            'user_id' => $user_id,
            'token' => $token
        ])) {
            return [
                'token'=> $token,
                'user_id'=> $user_id,
                'name'=> $name,
                'email'=> $email
            ];
        }
        return false;
    }
}