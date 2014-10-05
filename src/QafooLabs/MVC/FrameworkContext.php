<?php

namespace QafooLabs\MVC;

interface FrameworkContext
{
    /**
     * If a security context and token exists, retrieve the user id.
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @return string|integer
     */
    public function getCurrentUserId();

    /**
     * If a security context and token exists, retrieve the username.
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return string
     */
    public function getCurrentUsername();

    /**
     * Get the current User object
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser();

    /**
     * @return bool
     */
    public function hasToken();

    /**
     * @return bool
     */
    public function hasNonAnonymousToken();

    /**
     * Get the Security Token
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return \Symfony\Component\Securite\Core\Authentication\Token\TokenInterface
     */
    public function getToken();

    /**
     * @return bool
     */
    public function isGranted($attributes, $object = null);
}
