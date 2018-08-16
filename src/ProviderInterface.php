<?php namespace Nano7\Auth;

interface ProviderInterface
{
    /**
     * Get user by ID.
     *
     * @param $id
     * @return UserInterface|null
     */
    public function getById($id);

    /**
     * Get user by ID and remeber token
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return UserInterface|null
     */
    public function getByToken($id, $token);

    /**
     * Get user by credentials.
     *
     * @param  array  $credentials
     * @return UserInterface|null
     */
    public function getByCredentials(array $credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param  UserInterface $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials);

    /**
     * Get user by Access Token.
     *
     * @param  string $accessToken
     * @return UserInterface|null
     */
    public function getByAccessToken($accessToken);
}