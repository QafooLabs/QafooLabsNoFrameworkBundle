<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests;

use QafooLabs\Bundle\NoFrameworkBundle\MockFrameworkContext;

class MockFrameworkContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_grants_access_from_token_roles()
    {
        $user = \Phake::mock('Symfony\Component\Security\Core\User\UserInterface');
        \Phake::when($user)->getRoles()->thenReturn(array('ROLE_USER', 'ROLE_ADMIN'));

        $context = new MockFrameworkContext($user);

        $this->assertTrue($context->isGranted('ROLE_USER'));
    }
}
