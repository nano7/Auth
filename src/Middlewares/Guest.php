<?php namespace Nano7\Auth\Middlewares;

use Nano7\Http\Request;
use Nano7\Auth\AuthManager;

class Guest
{
    /**
     * @var AuthManager
     */
    protected $auth;

    /**
     * @param AuthManager $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @param null|string $guard
     */
    public function handle(Request $request, $next, $guard = null)
    {
        if ($this->auth->guard($guard)->check()) {
            return redirect()->home();
        }

        return $next($request);
    }
}