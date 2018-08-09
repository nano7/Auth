<?php namespace Nano7\Auth\Access;

use Nano7\Foundation\Support\Str;

/**
 * Class Gate
 * @package Nano7\Auth\Access
 * @method mixed getAttribute($attribute)
 */
trait Gate
{
    /**
     * @var bool
     */
    protected $loadedPermissions = false;

    /**
     * List all permissions of user.
     * @var array
     */
    protected $allows = [];

    /**
     * List all denies of user.
     * @var array
     */
    protected $denies = [];

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @return bool
     */
    public function allows($ability)
    {
        return $this->check($ability);
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param  string  $ability
     * @return bool
     */
    public function denies($ability)
    {
        return ! $this->allows($ability);
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param  array|string  $abilities
     * @return bool
     */
    public function check($abilities)
    {
        $abilities = (array) $abilities;
        foreach ($abilities as $ability) {
            if (! $this->raw($ability)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param  array|string  $abilities
     * @return bool
     */
    public function any($abilities)
    {
        $abilities = (array) $abilities;
        foreach ($abilities as $ability) {
            if ($this->raw($ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user have ability in permissions.
     *
     * @param $ability
     * @return bool
     */
    protected function raw($ability)
    {
        // Carregar lista de permissões do usuario
        if (! $this->loadedPermissions) {
            $this->loadPermissions();
        }

        // Verificar se existe alguma negacao explicita
        foreach ($this->denies as $deny) {
            if (Str::is($deny, $ability)) {
                return false;
            }
        }

        // Verificar se existe alguma permissao
        foreach ($this->allows as $allow) {
            if (Str::is($allow, $ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load all permissions of user.
     */
    protected function loadPermissions()
    {
        if (! method_exists($this, 'getPermissions')) {
            throw new \Exception("Method [getPermissions] not found");
        }

        // Caregar lista de permissoes e negacoes
        $roles = call_user_func_array([$this, 'getPermissions'], []);
        $roles = is_null($roles) ? (object)['allows' => [], 'denies' => []] : $roles;

        $this->allows = isset($roles->allows) ? $roles->allows : [];
        $this->denies = isset($roles->denies) ? $roles->denies : [];

        $this->loadedPermissions = true;
    }
}