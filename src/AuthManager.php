<?php namespace Nano7\Auth;

use Nano7\Foundation\Support\Arr;
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
     * @var \Closure
     */
    protected $userResolver;

    /**
     * @param $app
     * @param $defaultGuard
     */
    public function __construct($app, $defaultGuard)
    {
        $this->app = $app;
        $this->defaultGuard = $defaultGuard;

        $this->userResolver = function($guard = null) {
            return $this->guard($guard)->user();
        };
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

        // Procurar guard in connections
        $connection_config = config('auth.connections.' . $guard, []);
        $guard = Arr::get($connection_config, 'driver', $guard);

        // Verificar se guard ja foi criado
        if (array_key_exists($guard, $this->guards)) {
            return $this->guards[$guard];
        }

        // Verificar se provider do guard foi implementado
        if (array_key_exists($guard, $this->providers)) {
            return $this->guards[$guard] = call_user_func_array($this->providers[$guard], [$this->app, $connection_config]);
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
     * @param string|UserInterface $user
     */
    public function setCron($user)
    {
        // Verificar se deve carregar o usuario pelo id
        if (! is_object($user)) {
            $model = config('auth.model');
            $model = $this->app->make($model);
            $user = $model->query()->find($user);
        }

        if (! ($user instanceof UserInterface)) {
            throw new \Exception("User cron invalid");
        }

        $this->setDefaultGuard('console');
        $this->guard()->setUser($user);

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultGuard()
    {
        return $this->defaultGuard;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDefaultGuard($value)
    {
        $this->defaultGuard = $value;

        return $this;
    }

    /**
     * @return callable
     */
    public function userResolver()
    {
        return $this->userResolver;
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