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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CartBlamerListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $cartManager, CartContextInterface $cartContext)
    {
        $this->beConstructedWith($cartManager, $cartContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartBlamerListener::class);
    }

    function it_throws_exception_when_cart_does_not_implement_core_order_interface_on_implicit_login(
        BaseOrderInterface $order,
        CartContextInterface $cartContext,
        ShopUserInterface $user,
        UserEvent $userEvent
    ) {
        $cartContext->getCart()->willReturn($order);
        $userEvent->getUser()->willReturn($user);
        $this->shouldThrow(UnexpectedTypeException::class)->during('onImplicitLogin', [$userEvent]);
    }

    function it_throws_exception_when_cart_does_not_implement_core_order_interface_on_interactive_login(
        BaseOrderInterface $order,
        CartContextInterface $cartContext,
        InteractiveLoginEvent $interactiveLoginEvent,
        ShopUserInterface $user,
        TokenInterface $token
    ) {
        $cartContext->getCart()->willReturn($order);
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $this->shouldThrow(UnexpectedTypeException::class)->during('onInteractiveLogin', [$interactiveLoginEvent]);
    }

    function it_blames_cart_on_user_on_implicit_login(
        ObjectManager $cartManager,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        UserEvent $userEvent,
        ShopUserInterface $user,
        CustomerInterface $customer
    ) {
        $cartContext->getCart()->willReturn($cart);
        $userEvent->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $cart->setCustomer($customer)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();
        $this->onImplicitLogin($userEvent);
    }

    function it_blames_cart_on_user_on_interactive_login(
        ObjectManager $cartManager,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        ShopUserInterface $user,
        CustomerInterface $customer
    ) {
        $cartContext->getCart()->willReturn($cart);
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $cart->setCustomer($customer)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_does_nothing_if_given_user_is_invalid_on_interactive_login(
        CartContextInterface $cartContext,
        OrderInterface $cart,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token
    ) {
        $cartContext->getCart()->willReturn($cart);
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn('anon.');
        $cart->setCustomer(Argument::any())->shouldNotBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_implicit_login(
        CartContextInterface $cartContext,
        UserEvent $userEvent,
        ShopUserInterface $user
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $userEvent->getUser()->willReturn($user);
        $this->onImplicitLogin($userEvent);
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_interactive_login(
        CartContextInterface $cartContext,
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        ShopUserInterface $user
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
