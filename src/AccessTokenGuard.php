<?php namespace Nano7\Auth;

use Nano7\Http\Request;

class AccessTokenGuard extends Guard
{
    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Current access token.
     * @var string
     */
    protected $accessToken = '';

    /**
     * @var string
     */
    protected $inputKey = '';

    /**
     * @var string
     */
    protected $storageKey = '';

    /**
     * @param $app
     * @param Provider $provider
     * @param \Nano7\Foundation\Events\Dispatcher $events
     * @param Request $request
     * @param $inputKey
     * @param $storageKey
     */
    public function __construct($app, $provider, $events, Request $request, $inputKey, $storageKey)
    {
        parent::__construct($app, $provider, $events);
        
        $this->request = $request;
        $this->inputKey = $inputKey;
        $this->storageKey = $storageKey;
    }

    /**
     * @return UserInterface|null
     */
    protected function retrieve()
    {
        $token = $this->getToken();
        if (empty($token)) {
            return null;
        }

        return $this->loginByAccessToken($token);
    }

    /**
     * Retorna o token encontrado no request.
     *
     * @return null|string
     */
    protected function getToken()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        //if (empty($token)) {
        //    $token = $this->request->getPassword();
        //}

        return $token;
    }

    /**
     * @param $accessToken
     * @return UserInterface|null
     */
    public function loginByAccessToken($accessToken)
    {
        $this->accessToken = '';

        if (empty($accessToken)) {
            return null;
        }

        $user = $this->provider->getByAccessToken($accessToken);

        if (! is_null($user)) {
            $this->accessToken = $accessToken;
            $this->setUser($user);
        }

        return $user;
    }

    /**
     * Log the user out of the application by access token.
     *
     * @return void
     */
    public function logout()
    {
        if (empty($this->accessToken)) {
            return;
        }

        $user = $this->user();

        $user->forgetAccessToken($this->accessToken);

        // Disparar evento de logout
        $this->fireEvent('logout', $user);

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        //$this->loggedOut = true;
    }
}