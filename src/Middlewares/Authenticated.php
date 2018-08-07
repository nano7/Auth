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
        // Veriifcar NAO esta logado
        if (! $this->auth->guard($guard)->check()) {
            if (app()->runningWebApi()) {
                throw new \Exception('Unauthenticated');
            }

            return redirect()->guest(route('login'));
        }

        // Se esta logado, testar status atual do usuario
        try {
            $this->auth->guard($guard)->user()->testUser();
        } catch (\Exception $e) {
            if (app()->runningWebApi()) {
                throw $e;
            }

            return redirect()->guest(route('login'))->withStatus($e->getMessage());
        }

        return $next($request);
    }
}