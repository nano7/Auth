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
     * List all permissions of user.
     * @var array|null
     */
    protected $permissions;

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
        if (is_null($this->permissions)) {
            $this->loadPermissions();
        }

        foreach ($this->permissions as $permission) {
            if (Str::is($permission, $ability)) {
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
        $permissions = $this->getAttribute('permissions');
        $this->permissions = is_null($permissions) ? [] : $permissions;
    }
}