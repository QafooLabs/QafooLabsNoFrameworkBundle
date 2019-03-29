<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests;

use PHPUnit\Framework\TestCase;
use QafooLabs\Bundle\NoFrameworkBundle\SymfonyTokenContext;

class SymfonyTokenContextTest extends TestCase
{
    private $tokenStorage;
    private $authorizationChecker;

    public function setUp() : void
    {
        $this->tokenStorage = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $this->authorizationChecker = \Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        parent::setUp();
    }
    /**
     * @test
     */
    public function it_retrieves_token_from_security_context()
    {
        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        \Phake::when($this->tokenStorage)->getToken()->thenReturn($token);

        $context = new SymfonyTokenContext($this->tokenStorage, $this->authorizationChecker);

        $this->assertTrue($context->hasToken());
        $this->assertSame($token, $context->getToken());
    }

    /**
     * @test
     */
    public function it_throws_unauthenticated_user_exception_when_no_token()
    {
        $context = new SymfonyTokenContext($this->tokenStorage, $this->authorizationChecker);

        $this->expectException('QafooLabs\MVC\Exception\UnauthenticatedUserException');

        $context->getToken();
    }

    /**
     * @test
     */
    public function it_allows_check_has_token()
    {
        $context = new SymfonyTokenContext($this->tokenStorage, $this->authorizationChecker);

        $this->assertFalse($context->hasToken());
    }
}
