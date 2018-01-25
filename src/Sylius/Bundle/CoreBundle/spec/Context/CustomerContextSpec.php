<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class CustomerContextSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->beConstructedWith($tokenStorage, $authorizationChecker);
    }

    function it_gets_customer_from_currently_logged_user(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenInterface $token,
        ShopUserInterface $user,
        CustomerInterface $customer
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $this->getCustomer()->shouldReturn($customer);
    }

    function it_returns_null_if_user_is_not_logged_in($tokenStorage): void
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->getCustomer()->shouldReturn(null);
    }
}
