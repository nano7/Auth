<?php

if (! function_exists('auth')) {
    /**
     * @param null $guard
     * @return \Nano7\Auth\Guard|\Nano7\Auth\AuthManager
     */
    function auth($guard = null)
    {
        $auth = app('auth');

        if (is_null($guard)) {
            return $auth;
        }

        return $auth->guard($guard);
    }
}