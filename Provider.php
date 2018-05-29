<?php namespace Nano7\Auth;

use Nano7\Database\Model\Model;
use Nano7\Foundation\Application;
use Nano7\Foundation\Support\Arr;

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
     * @param $app
     * @param $model
     */
    public function __construct($app, $model)
    {
        $this->app = $app;
        $this->model = $model;
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
}