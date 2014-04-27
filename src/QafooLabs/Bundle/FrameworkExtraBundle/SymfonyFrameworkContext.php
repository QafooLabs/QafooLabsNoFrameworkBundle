<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle;

use QafooLabs\MVC\FrameworkContext;
use QafooLabs\MVC\Exception;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SymfonyFrameworkContext implements FrameworkContext
{
    private $securityContext;
    private $environment;
    private $debug;

    public function __construct(SecurityContextInterface $securityContext, $environment, $debug)
    {
        $this->securityContext = $securityContext;
        $this->environment = $environment;
        $this->debug = $debug;
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
        return $this->getUser()->getId();
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

    public function hasToken()
    {
        return $this->securityContext->getToken() !== null;
    }

    /**
     * Get the Security Token
     *
     * Throws UnauthenticatedUserException when no valid token exists.
     *
     * @throws \QafooLabs\MVC\Exception\UnauthenticatedUserException
     * @return \Symfony\Component\Securite\Core\Authentication\Token\TokenInterface
     */
    public function getToken()
    {
        $token = $this->securityContext->getToken();

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
        return $this->securityContext->isGranted($attributes, $object);
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }
}
