<?php namespace Nano7\Auth;

interface UserInterface
{
    /**
     * Retorna o ID do usuario.
     *
     * @return string
     */
    public function getAuthId();

    /**
     * Retorna o nome do usuario.
     *
     * @return string
     */
    public function getAuthName();

    /**
     * Retorna a senha do usuario.
     *
     * @return string
     */
    public function getAuthPassword();

    /**
     * Retorna o remeber token
     *
     * @return string
     */
    public function getRememberToken();
}