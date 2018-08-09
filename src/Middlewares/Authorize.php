<?php namespace Nano7\Auth\Middlewares;

use Nano7\Http\Request;
use Nano7\Auth\AuthManager;

class Authorize
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
     * @param string $ability
     * @param null|string $guard
     */
    public function handle(Request $request, $next, $ability, $guard = null)
    {
        // Veriifcar se esta logado
        $this->checkAuthenticate($guard);

        // Verificar permissoes
        $user = $this->auth->guard($guard)->user();
        if (! $user->can($ability)) {
            throw new \Exception('Not authorized');
        }

        return $next($request);
    }

    protected function checkAuthenticate($guard)
    {
        // Veriifcar se esta logado
        if (! $this->auth->guard($guard)->check()) {
            throw new \Exception('Not authorized');
        }
    }
}