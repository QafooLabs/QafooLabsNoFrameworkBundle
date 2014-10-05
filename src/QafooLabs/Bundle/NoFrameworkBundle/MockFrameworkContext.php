<?php

namespace QafooLabs\Bundle\NoFrameworkBundle;

use QafooLabs\MVC\FrameworkContext;
use QafooLabs\MVC\Exception;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MockFrameworkContext implements FrameworkContext
{
    private $user;
    private $token;

    public function __construct(UserInterface $user = null)
    {
        $this->user = $user;
    }

    /**
     * If a security context and token exists, retrieve the user id.
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @return string|integer
     */
    public function getCurrentUserId()
    {
        return $this->user->getId();
    }

    /**
     * If a security context and token exists, retrieve the username.
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return string
     */
    public function getCurrentUsername()
    {
        return $this->user->getUsername();
    }

    /**
     * Get the current User object
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser()
    {
        if (!is_object($this->user)) {
            throw new Exception\UnauthenticatedUserException();
        }

        return $this->user;
    }

    public function hasToken()
    {
        return true;
    }

    public function hasNonAnonymousToken()
    {
        return false;
    }

    public function getToken()
    {
        throw new \BadMethodCallException("getToken() not supported in MockFrameworkContext");
    }

    public function isGranted($attributes, $object = null)
    {
        if (!is_string($attributes) && strpos($attributes, 'ROLE_') === false) {
            throw new \BadMethodCallException("Only ROLE_* checks are possible with mock interface.");
        }

        $roles = $this->user->getRoles();

        foreach ($roles as $role) {
            if ((string)$role === $attributes) {
                return true;
            }
        }

        return false;
    }
}
