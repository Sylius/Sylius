<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @mixin CartBlamerListener
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CartBlamerListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $cartManager, CartProviderInterface $cartProvider)
    {
        $this->beConstructedWith($cartManager, $cartProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener');
    }

    function it_throws_exception_when_cart_does_not_implement_core_order_interface_on_implicit_login(
        CartProviderInterface $cartProvider,
        CartInterface $cart,
        UserEvent $userEvent,
        UserInterface $user
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $userEvent->getUser()->willReturn($user);

        $this->shouldThrow(UnexpectedTypeException::class)->during('onImplicitLogin', [$userEvent]);
    }

    function it_throws_exception_when_cart_does_not_implement_core_order_interface_on_interactive_login(
        CartProviderInterface $cartProvider,
        CartInterface $cart,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        UserInterface $user
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->shouldThrow(UnexpectedTypeException::class)->during('onInteractiveLogin', [$interactiveLoginEvent]);
    }

    function it_blames_cart_on_user_on_implicit_login(
        ObjectManager $cartManager,
        CartProviderInterface $cartProvider,
        OrderInterface $cart,
        UserEvent $userEvent,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $userEvent->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();

        $this->onImplicitLogin($userEvent);
    }

    function it_blames_cart_on_user_on_interactive_login(
        ObjectManager $cartManager,
        CartProviderInterface $cartProvider,
        OrderInterface $cart,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_does_nothing_if_given_user_is_invalid_on_interactive_login(
        CartProviderInterface $cartProvider,
        OrderInterface $cart,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn('anon.');

        $cart->setCustomer(Argument::any())->shouldNotBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_implicit_login(
        CartProviderInterface $cartProvider,
        UserEvent $userEvent,
        UserInterface $user
    ) {
        $cartProvider->hasCart()->willReturn(false);

        $userEvent->getUser()->willReturn($user);

        $this->onImplicitLogin($userEvent);
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_interactive_login(
        CartProviderInterface $cartProvider,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        UserInterface $user
    ) {
        $cartProvider->hasCart()->willReturn(false);

        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
