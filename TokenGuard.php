<?php namespace Nano7\Auth;

use Illuminate\Http\Request;

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
     * @param $provider
     * @param Request $request
     * @param callable $callback
     */
    public function __construct($app, $provider, Request $request, $inputKey, $storageKey)
    {
        parent::__construct($app, $provider);
        
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

        return $this->provider->getByCredentials([$this->storageKey => $token]);
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

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        return $token;
    }
}