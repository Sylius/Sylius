<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SecurityIdentityProviderSpec extends ObjectBehavior
{
    function let(SecurityContextInterface $securityContext)
    {
        $this->beConstructedWith($securityContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Provider\SecurityIdentityProvider');
    }

    function it_is_a_rbac_identity_provider()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface');
    }

    function it_returns_null_if_user_is_not_logged_in($securityContext)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn(null);

        $this->getIdentity()->shouldReturn(null);
    }

    function it_returns_null_if_token_exists_but_still_no_authenticated_user($securityContext, TokenInterface $token)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn(null);

        $this->getIdentity()->shouldReturn(null);
    }

    function it_returns_the_authenticated_user($securityContext, TokenInterface $token, IdentityInterface $user)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);

        $this->getIdentity()->shouldReturn($user);
    }

    function it_throws_exception_if_user_does_not_implement_identity_interface($securityContext, TokenInterface $token, UserInterface $user)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);

        $this
            ->shouldThrow(new \InvalidArgumentException('User class must implement "Sylius\Component\Rbac\Model\IdentityInterface".'))
            ->duringGetIdentity()
        ;
    }
}
