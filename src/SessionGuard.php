<?php namespace Nano7\Auth;

use Illuminate\Http\Request;

class SessionGuard extends Guard
{
    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param $app
     * @param $provider
     * @param Request $request
     * @param callable $callback
     */
    public function __construct($app, $provider, Request $request, $name)
    {
        parent::__construct($app, $provider);
        
        $this->request = $request;
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
        $sessionId = session()->get($this->name);

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
        session()->put($this->name, $user->getAuthId());

        // Tratar remember
        //...

        // Disparar evento de login
        //..

        // Guardar usuario
        $this->setUser($user);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        //$user = $this->user();

        // Remover id da sessao
        session()->remove($this->name);

        // Zerar remember
        //..

        // DIsparar evento de logout
        //..

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        //$this->loggedOut = true;
    }
}