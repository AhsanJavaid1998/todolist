<?php

namespace App\Contracts;

interface AuthContract
{
    public function login($request);

    public function register($request);
    public function logout($request);
    public function emailVerification($request);

}
