<?php

namespace App\Http\Controllers;

use App\Contracts\AuthContract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $_auth;
    public function __construct(AuthContract $auth)
    {
        $this->_auth = $auth;
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request->only('email', 'password');
        return $this->_auth->login($data);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password', 'password_confirmation');
        return $this->_auth->register($data);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {
        $data = $request->only('token');
        return $this->_auth->logout($data);
    }

    public function emailVerification(Request $request)
    {
        $data = $request->only('code');
        return $this->_auth->emailVerification($data);
    }
}
