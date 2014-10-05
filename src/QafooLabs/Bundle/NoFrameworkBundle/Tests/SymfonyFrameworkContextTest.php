<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests;

use QafooLabs\Bundle\NoFrameworkBundle\SymfonyFrameworkContext;

class SymfonyFrameworkContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_retrieves_token_from_security_context()
    {
        $security = \Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface');
        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $context = new SymfonyFrameworkContext($security, 'dev', true);

        \Phake::when($security)->getToken()->thenReturn($token);

        $this->assertTrue($context->hasToken());
        $this->assertSame($token, $context->getToken());
    }

    /**
     * @test
     */
    public function it_throws_unauthenticated_user_exception_when_no_token()
    {
        $security = \Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface');
        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $context = new SymfonyFrameworkContext($security, 'dev', true);

        $this->setExpectedException('QafooLabs\MVC\Exception\UnauthenticatedUserException');

        $context->getToken();
    }

    /**
     * @test
     */
    public function it_allows_check_has_token()
    {
        $security = \Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface');
        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $context = new SymfonyFrameworkContext($security, 'dev', true);

        $this->assertFalse($context->hasToken());
    }
}
