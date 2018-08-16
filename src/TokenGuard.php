<?php namespace Nano7\Auth;

use Nano7\Http\Request;

class TokenGuard extends Guard
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
    protected $inputKey = '';

    /**
     * @var string
     */
    protected $storageKey = '';

    /**
     * @param $app
     * @param ProviderInterface $provider
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

        $user = $this->provider->getByCredentials([$this->storageKey => $token]);

        $this->setUser($user);

        return $user;
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
}