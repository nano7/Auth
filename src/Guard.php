<?php namespace Nano7\Auth;

use Nano7\Foundation\Application;

abstract class Guard
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @param $app
     * @param Provider $provider
     */
    public function __construct($app, Provider $provider)
    {
        $this->app = $app;
        $this->provider = $provider;
    }

    /**
     * Retorna usuario logado.
     *
     * @return UserInterface
     */
    public function user()
    {
        // Verificar se ja esta carregado
        if (! is_null($this->user)) {
            return $this->user;
        }

        // Carregar usuario
        $this->setUser($user = $this->retrieve());

        return $user;
    }

    /**
     * Retorna se usuario esta logado.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Retorna se usuario não esta logado (ANONIMO).
     *
     * @return bool
     */
    public function guest()
    {
        return is_null($this->user());
    }

    /**
     * Set the current user.
     *
     * @param  UserInterface  $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        //$this->loggedOut = false;

        //$this->fireAuthenticatedEvent($user);

        return $this;
    }

    /**
     * @return UserInterface
     */
    abstract protected function retrieve();
}