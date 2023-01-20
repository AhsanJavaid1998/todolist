<?php

namespace App\services;

use App\Contracts\AuthContract;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthContract
{
    use ApiResponseTrait;
    public function login($request){
        //valid credential
        $validator = Validator::make($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:8'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response($token ?? null, false, $validator->messages());
        }
        //Request is validated
        //Create token
        try {
            if (! $token = JWTAuth::attempt($request)) {
                $status = false;
                $message = 'Login credentials are invalid.';
            }else{
                if(Auth::user()->is_email_verified == 0)
                {
                    $message = 'Login successfully. You need to verify your account. We have sent you a verification code, please check your email.';
                } else{
                    $message = 'Login successfully';
                }
                $status = true;
            }
        } catch (JWTException $e) {
            $status = false;
            $message = 'Could not create token.';
        }
        //User login, return success response
        return ApiResponseTrait::response($token ?? null, $status, $message ?? null);

    }

    public function register($request)
    {
        //Validate data
        $validator = Validator::make($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6|max:8'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        //Request is valid, create new user
        try {
            $request['password'] = bcrypt($request['password']);
            $request['verification_code'] = random_int(100000, 999999);
            $user = User::create($request);
            $data = [
                'email' => $user['email'],
                'code' => $user['verification_code']
            ];
            dispatch(new SendEmailJob($data));
            $status = true;
            $message = 'User created successfully. We have sent you a verification code, please check your email.';
        } catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        //User created, return success response
        return ApiResponseTrait::response($user ?? null, $status, $message ?? null);

    }
    public function logout($request) {
        //valid credential
        $validator = Validator::make($request, [
            'token' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request['token']);
            $status = true;
            $message = 'User has been logged out';
        } catch (JWTException $exception) {
            $status = false;
            $message = 'Sorry, user cannot be logged out';
        }
        return ApiResponseTrait::response( null, $status, $message ?? null);
    }

    public function emailVerification($request)
    {
        $validator = Validator::make($request, [
            'code' => 'required|numeric|min:6'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        try {
            $user = Auth::user();
            if ($user->verification_code == $request['code'])
            {
                $user->markEmailAsVerified();
                $user->update([
                    'is_email_verified' => 1
                ]);
                $status = true;
                $message = 'Email verified successfully';
            }else{
                $user = [];
                $status = true;
                $message = 'Invalid verification code';
            }
        } catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($user ?? null, $status, $message ?? null);

    }
}
