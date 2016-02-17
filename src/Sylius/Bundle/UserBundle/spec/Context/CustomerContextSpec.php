<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerContextSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->beConstructedWith($tokenStorage, $authorizationChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Context\CustomerContext');
    }

    function it_gets_customer_from_currently_logged_user($tokenStorage, $authorizationChecker, TokenInterface $token, UserInterface $user, CustomerInterface $customer)
    {
        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $this->getCustomer()->shouldReturn($customer);
    }

    function it_returns_null_if_user_is_not_logged_in($tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->getCustomer()->shouldReturn(null);
    }
}
