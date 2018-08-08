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

    /**
     * Validar se usuario pode ser logado.
     *
     * @return void
     */
    public function testUser();

    /**
     * Get user by access token.
     *
     * @param string $token
     * @return UserInterface
     */
    public static function getByAccessToken($token);
}