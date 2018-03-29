<?php
/**
 * @author: Mahabubul Hasan <codehasan@gmail.com>
 * @date: 3/30/2018 2:48 AM
 */

namespace Uzzal\ApiToken;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait Authenticate
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

    /**
     * @param Guard $auth
     * @param AuthService $service
     * @param Request $request
     * @return array
     */
    public function store(Guard $auth, AuthService $service, Request $request)
    {
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return ['status'=>'failed', 'msg'=>$validator->messages()];
        }

        $credentials = $request->only('email', 'password');
        if($auth->attempt($credentials, $request->has('remember'))){
            return array_merge(['status'=>'success'], $service->generateToken());
        }else{
            return ['status'=>'failed','msg'=>'Invalid email or password'];
        }
    }
}