<?php namespace Nano7\Auth;

use Nano7\Http\Request;
use Nano7\Http\Session\StoreInterface as Session;

class SessionGuard extends Guard
{
    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param $app
     * @param $provider
     * @param $events
     * @param Request $request
     * @param Session $session
     * @param callable $callback
     */
    public function __construct($app, $provider, $events, Request $request, Session $session, $name)
    {
        parent::__construct($app, $provider, $events);
        
        $this->request = $request;
        $this->session = $session;
        $this->name = $name;
    }

    /**
     * @return UserInterface|null
     */
    protected function retrieve()
    {
        $id = $this->getSessionId();
        if (empty($id)) {
            return null;
        }

        return $this->provider->getById($id);
    }

    /**
     * Retorna id na sessao.
     *
     * @return null|string
     */
    protected function getSessionId()
    {
        $sessionId = $this->session->get($this->name);

        return $sessionId;
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed  $id
     * @param  bool   $remember
     * @return UserInterface|bool
     */
    public function loginUsingId($id, $remember = false)
    {
        if (! is_null($user = $this->provider->getById($id))) {
            $this->login($user, $remember);

            return $user;
        }

        return false;
    }

    /**
     * Executar o login.
     *
     * @param UserInterface $user
     * @param bool $remember
     * @return void
     */
    public function login(UserInterface $user, $remember = false)
    {
        // Atualizar sessao
        $this->session->set($this->name, $user->getAuthId());

        // Tratar remember
        //...

        // Disparar evento de login
        $this->fireEvent('login', $user, $remember);

        // Guardar usuario
        $this->setUser($user);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->fireEvent('attempt', $credentials, $remember);

        $this->lastAttempted = $user = $this->provider->getByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);

            return true;
        }

        // If the authentication attempt fails we will fire an event so that the user
        // may be notified of any suspicious attempts to access their account from
        // an unrecognized user. A developer may listen to this event as needed.
        $this->fireEvent('failed', $user, $credentials);

        return false;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $this->lastAttempted = $user = $this->provider->getByCredentials($credentials);

        return $this->hasValidCredentials($user, $credentials);
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  mixed  $user
     * @param  array  $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return ! is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        // Remover id da sessao
        $this->session->forget($this->name);

        // Zerar remember
        //..

        // Disparar evento de logout
        $this->fireEvent('logout', $user);

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        //$this->loggedOut = true;
    }
}