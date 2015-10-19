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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerContextSpec extends ObjectBehavior
{
    function let(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        SessionInterface $session
    ) {
        $this->beConstructedWith(
            $tokenStorage,
            $authorizationChecker,
            $session
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Context\CustomerContext');
    }

    function it_gets_customer_from_currently_logged_in_user(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenInterface $token,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);

        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $this->getCustomer()->shouldReturn($customer);
    }

    function it_stores_customer_in_session($session, CustomerInterface $customer)
    {
        $session->set('customer', $customer)->shouldBeCalled();
        $this->setCustomer($customer);
    }

    function it_returns_previously_set_customer_from_session_if_user_is_not_logged_in(
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        CustomerInterface $customer
    ) {
        $tokenStorage->getToken()->willReturn(null);
        $session->set('customer', $customer)->shouldBeCalled();
        $session->get('customer')->willReturn($customer);

        $this->setCustomer($customer);
        $this->getCustomer()->shouldReturn($customer);
    }

    function it_returns_customer_from_logged_in_user_even_if_customer_is_previously_set_in_session(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        CustomerInterface $customerOfUser,
        CustomerInterface $customerInSession,
        TokenInterface $token,
        UserInterface $user
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customerOfUser);

        $this->setCustomer($customerInSession);
        $this->getCustomer()->shouldReturn($customerOfUser);
    }

    function it_returns_null_if_user_is_not_logged_in_and_customer_not_set_in_session(TokenStorageInterface $tokenStorage) {
        $tokenStorage->getToken()->willReturn(null);
        $this->getCustomer();
    }
}
