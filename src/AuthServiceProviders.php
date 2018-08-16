<?php namespace Nano7\Auth;

use Nano7\Http\Kernel;
use Nano7\Foundation\Support\Arr;
use Nano7\Foundation\Support\ServiceProvider;

class AuthServiceProviders extends ServiceProvider
{
    protected $defaultUserprovider = 'model';

    /**
     * Register objetos do auth.
     */
    public function register()
    {
        $this->registerManager();

        $this->registerMiddlewares();
    }

    /**
     * Registrar manager do auth.
     */
    protected function registerManager()
    {
        $this->app->singleton('auth', function ($app) {
            $config = $app['config'];

            $auth = new AuthManager($app, $config->get('auth.default'));

            // User Providers
            $this->registerProviderModel($auth);

            // Guards
            $this->registerGuardSession($auth);
            $this->registerGuardToken($auth);
            $this->registerGuardAccessToken($auth);
            $this->registerGuardConsole($auth);

            return $auth;
        });

        $this->app->alias('auth', 'Nano7\Auth\AuthManager');
    }

    /**
     * Registrar provider: model.
     *
     * @param AuthManager $auth
     */
    protected function registerProviderModel(AuthManager $auth)
    {
        $auth->extendProvider('model', function($app, $config) {
            return new ModelProvider(
                $app,
                Arr::get($config, 'model', '\App\Models\User'),
                $app['bcrypt']
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param $model
     */
    protected function registerGuardSession(AuthManager $auth)
    {
        $auth->extend('session', function($app, $config) use ($auth) {
            return new SessionGuard(
                $app,
                $auth->provider(Arr::get($config, 'provider', $this->defaultUserprovider)),
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                $app['session'],
                Arr::get($config, 'sessionName', 'nano7_session')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param $model
     */
    protected function registerGuardToken(AuthManager $auth)
    {
        $auth->extend('token', function($app, $config) use ($auth) {
            return new TokenGuard(
                $app,
                $auth->provider(Arr::get($config, 'provider', $this->defaultUserprovider)),
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                Arr::get($config, 'inputKey', 'access_token'),
                Arr::get($config, 'storageKey', 'api_token')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param $model
     */
    protected function registerGuardAccessToken(AuthManager $auth)
    {
        $auth->extend('accesstoken', function($app, $config) use ($auth) {
            return new AccessTokenGuard(
                $app,
                $auth->provider(Arr::get($config, 'provider', $this->defaultUserprovider)),
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                Arr::get($config, 'inputKey', 'access_token'),
                Arr::get($config, 'storageKey', 'api_token')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param $model
     */
    protected function registerGuardConsole(AuthManager $auth)
    {
        $auth->extend('console', function($app, $config) use ($auth) {
            return new ConsoleGuard(
                $app,
                $auth->provider(Arr::get($config, 'provider', $this->defaultUserprovider)),
                $app['events']
            );
        });
    }

    /**
     * Register middlewares.
     *
     * @return void
     */
    protected function registerMiddlewares()
    {
        event()->listen('web.middleware.register', function (Kernel $web) {

            $web->middleware('auth',  '\Nano7\Auth\Middlewares\Authenticated');
            $web->middleware('guest', '\Nano7\Auth\Middlewares\Guest');
            $web->middleware('can',   '\Nano7\Auth\Middlewares\Authorize');
        });
    }
}