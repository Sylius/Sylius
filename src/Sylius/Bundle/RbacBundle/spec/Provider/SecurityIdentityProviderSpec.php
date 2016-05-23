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
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityIdentityProviderSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Provider\SecurityIdentityProvider');
    }

    function it_is_a_rbac_identity_provider()
    {
        $this->shouldHaveType(CurrentIdentityProviderInterface::class);
    }

    function it_returns_null_if_user_is_not_logged_in($tokenStorage)
    {
        $tokenStorage->getToken()->shouldBeCalled()->willReturn(null);

        $this->getIdentity()->shouldReturn(null);
    }

    function it_returns_null_if_token_exists_but_still_no_authenticated_user($tokenStorage, TokenInterface $token)
    {
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn(null);

        $this->getIdentity()->shouldReturn(null);
    }

    function it_returns_null_if_token_exists_but_its_an_anonymous_user($tokenStorage, AnonymousToken $token)
    {
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $this->getIdentity()->shouldReturn(null);
    }

    function it_returns_the_authenticated_user($tokenStorage, TokenInterface $token, IdentityInterface $user)
    {
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);

        $this->getIdentity()->shouldReturn($user);
    }

    function it_throws_exception_if_user_does_not_implement_identity_interface($tokenStorage, TokenInterface $token, UserInterface $user)
    {
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);

        $this
            ->shouldThrow(new \InvalidArgumentException('User class must implement "Sylius\Component\Rbac\Model\IdentityInterface".'))
            ->duringGetIdentity()
        ;
    }
}
