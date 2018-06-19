<?php namespace Nano7\Auth;

use Nano7\Foundation\Config\Repository;
use Nano7\Foundation\Support\ServiceProvider;

class AuthServiceProviders extends ServiceProvider
{
    /**
     * Register objetos do auth.
     */
    public function register()
    {
        $this->registerProvider();

        $this->registerManager();
    }

    /**
     * Registrar manager do auth.
     */
    protected function registerManager()
    {
        $this->app->singleton('auth', function ($app) {
            $config = $app['config'];

            $auth = new AuthManager($app, $config->get('auth.default'));

            $this->registerGuardWeb($auth, $config);

            $this->registerGuardApi($auth, $config);

            return $auth;
        });
    }

    /**
     * Registrar provider.
     */
    protected function registerProvider()
    {
        $this->app->bind('auth.provider', function($app) {
            return new Provider($app, $app['config']->get('auth.model', '\App\Models\User'));
        });
    }

    /**
     * @param AuthManager $auth
     * @param Repository $config
     * @param $model
     */
    protected function registerGuardApi(AuthManager $auth, Repository $config)
    {
        $auth->extend('api', function($app) use ($config) {

            // Guard do token
            return new TokenGuard(
                $app,
                $app['auth.provider'],
                $app['request'],
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
    protected function registerGuardWeb(AuthManager $auth, Repository $config)
    {
        $auth->extend('web', function($app) use ($config) {

            // Guard do token
            return new SessionGuard(
                $app,
                $app['auth.provider'],
                $app['request'],
                $config->get('auth.session.name', 'netforce_session')
            );
        });
    }
}