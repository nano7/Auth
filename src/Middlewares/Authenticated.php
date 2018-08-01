<?php namespace Nano7\Auth\Middlewares;

use Nano7\Http\Request;
use Nano7\Auth\AuthManager;

class Authenticated
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
        if (! $this->auth->guard($guard)->check()) {
            if (app()->runningWebApi()) {
                throw new \Exception('Unauthenticated');
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}