<?php namespace Nano7\Auth;

use Nano7\Foundation\Application;

class AuthManager
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $defaultGuard = '';

    /**
     * @var array
     */
    protected $guards = [];

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @param $app
     * @param $defaultGuard
     */
    public function __construct($app, $defaultGuard)
    {
        $this->app = $app;
        $this->defaultGuard = $defaultGuard;
    }

    /**
     * Retorna guard.
     *
     * @param null $guard
     * @return Guard
     * @throws \Exception
     */
    public function guard($guard = null)
    {
        $guard = is_null($guard) ? $this->defaultGuard : $guard;

        // Verificar se guard ja foi criado
        if (array_key_exists($guard, $this->guards)) {
            return $this->guards[$guard];
        }

        // Verificar se provider do guard foi implementado
        if (array_key_exists($guard, $this->providers)) {
            return $this->guards[$guard] = call_user_func_array($this->providers[$guard], [$this->app]);
        }

        throw new \Exception("guard provider [$guard] is invalid");
    }

    /**
     * Register a custom guard name Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($guardName, \Closure $callback)
    {
        $this->providers[$guardName] = $callback;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->guard(), $name], $arguments);
    }
}