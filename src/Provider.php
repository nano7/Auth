<?php namespace Nano7\Auth;

use Nano7\Database\Model\Model;
use Nano7\Foundation\Support\Arr;
use Nano7\Foundation\Application;
use Nano7\Foundation\Encryption\BcryptHasher;

class Provider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var BcryptHasher
     */
    protected $hasher;

    /**
     * @param $app
     * @param $model
     */
    public function __construct($app, $model, BcryptHasher $hasher)
    {
        $this->app = $app;
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Model
     */
    public function model()
    {
        return $this->app[$this->model];
    }

    /**
     * Get user by ID.
     *
     * @param $id
     * @return UserInterface|null
     */
    public function getById($id)
    {
        return $this->model()->query()->find($id);
    }

    /**
     * Get user by ID and remeber token
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return UserInterface|null
     */
    public function getByToken($id, $token)
    {
        $model = $this->getById($id);

        if (is_null($model)) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && ($rememberToken == $token) ? $model : null;
    }

    /**
     * Get user by credentials.
     *
     * @param  array  $credentials
     * @return UserInterface|null
     */
    public function getByCredentials(array $credentials)
    {
        $credentials = (array) $credentials;
        $credentials = Arr::except($credentials, ['password']);

        if (count($credentials) == 0) {
            return null;
        }

        $query = $this->model()->query();
        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  UserInterface $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Get user by Access Token.
     *
     * @param  string $accessToken
     * @return UserInterface|null
     */
    public function getByAccessToken($accessToken)
    {
        return call_user_func_array([$this->getModel(), 'getByAccessToken'], [$accessToken]);
    }
}