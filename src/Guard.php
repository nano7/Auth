<?php namespace Nano7\Auth;

use Nano7\Foundation\Application;
use Nano7\Foundation\Events\Dispatcher;

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
     * @var Dispatcher
     */
    protected $events;

    /**
     * @param $app
     * @param Provider $provider
     * @param Dispatcher $events
     */
    public function __construct($app, Provider $provider, Dispatcher $events)
    {
        $this->app = $app;
        $this->provider = $provider;
        $this->events = $events;
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
     * Return id of user.
     * @return null|string
     */
    public function id()
    {
        if (! $this->check()) {
            return null;
        }

        return $this->user()->getAuthId();
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
     * @param  UserInterface|null  $user
     * @return $this
     */
    public function setUser($user)
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

    /**
     * Fire event auth.
     *
     * @param $event
     * @return void
     */
    protected function fireEvent($event)
    {
        $args = func_get_args();
        array_shift($args);

        $id = sprintf('auth.%s', $event);
        if ($this->events) {
            $this->events->fire($id, $args);
        }
    }
}