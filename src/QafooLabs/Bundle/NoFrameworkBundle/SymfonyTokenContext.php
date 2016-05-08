<?php

namespace QafooLabs\Bundle\NoFrameworkBundle;

use QafooLabs\MVC\TokenContext;
use QafooLabs\MVC\Exception;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SymfonyTokenContext implements TokenContext
{
    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
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
        return $this->getCurrentUser()->getId();
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
        return $this->getToken()->getUsername();
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
        $user = $this->getToken()->getUser();

        if (!is_object($user)) {
            throw new Exception\UnauthenticatedUserException();
        }

        return $user;
    }

    /**
     * @return bool
     */
    public function hasToken()
    {
        return $this->tokenStorage->getToken() !== null;
    }

    /**
     * @return bool
     */
    public function hasNonAnonymousToken()
    {
        return $this->hasToken() && ! ($this->getToken() instanceof AnonymousToken);
    }

    /**
     * Get the Security Token
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    public function getToken()
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new Exception\UnauthenticatedUserException();
        }

        return $token;
    }

    /**
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    public function assertIsGranted($attributes, $object = null)
    {
        if (!$this->isGranted($attributes, $object)) {
            throw new AccessDeniedHttpException();
        }
    }
}
