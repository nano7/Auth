<?php namespace Nano7\Auth;

use Nano7\Foundation\Config\Repository;
use Nano7\Foundation\Support\ServiceProvider;
use Nano7\Http\Kernel;

class AuthServiceProviders extends ServiceProvider
{
    /**
     * Register objetos do auth.
     */
    public function register()
    {
        $this->registerProvider();

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

            $this->registerGuardSession($auth, $config);

            $this->registerGuardToken($auth, $config);

            $this->registerGuardAccessToken($auth, $config);

            $this->registerGuardConsole($auth, $config);

            return $auth;
        });

        $this->app->alias('auth', 'Nano7\Auth\AuthManager');
    }

    /**
     * Registrar provider.
     */
    protected function registerProvider()
    {
        $this->app->bind('auth.provider', function($app) {
            return new Provider(
                $app,
                $app['config']->get('auth.model', '\App\Models\User'),
                $app['bcrypt']
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param Repository $config
     * @param $model
     */
    protected function registerGuardToken(AuthManager $auth, Repository $config)
    {
        $auth->extend('token', function($app) use ($config) {

            // Guard do token
            return new TokenGuard(
                $app,
                $app['auth.provider'],
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                $config->get('auth.token.inputKey', 'access_token'),
                $config->get('auth.token.storageKey', 'api_token')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param Repository $config
     * @param $model
     */
    protected function registerGuardAccessToken(AuthManager $auth, Repository $config)
    {
        $auth->extend('accesstoken', function($app) use ($config) {

            // Guard do accesstoken
            return new AccessTokenGuard(
                $app,
                $app['auth.provider'],
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                $config->get('auth.token.inputKey', 'access_token'),
                $config->get('auth.token.storageKey', 'api_token')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param Repository $config
     * @param $model
     */
    protected function registerGuardSession(AuthManager $auth, Repository $config)
    {
        $auth->extend('session', function($app) use ($config) {

            // Guard session
            return new SessionGuard(
                $app,
                $app['auth.provider'],
                $app['events'],
                $app->resolved('request') ? $app['request'] : null,
                $app['session'],
                $config->get('auth.session.name', 'netforce_session')
            );
        });
    }

    /**
     * @param AuthManager $auth
     * @param Repository $config
     * @param $model
     */
    protected function registerGuardConsole(AuthManager $auth, Repository $config)
    {
        $auth->extend('console', function($app) use ($config) {

            // Guard do console
            return new ConsoleGuard(
                $app,
                $app['auth.provider'],
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