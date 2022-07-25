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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class ShopCartBlamerListenerSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
    ): void {
        $this->beConstructedWith($cartContext, $sectionResolver);
    }

    function it_throws_an_exception_when_cart_does_not_implement_core_order_interface_on_implicit_login(
        BaseOrderInterface $order,
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        ShopUserInterface $user,
        UserEvent $userEvent,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($order);
        $userEvent->getUser()->willReturn($user);

        $this->shouldThrow(UnexpectedTypeException::class)->during('onImplicitLogin', [$userEvent]);
    }

    function it_throws_an_exception_when_cart_does_not_implement_core_order_interface_on_interactive_login(
        BaseOrderInterface $order,
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        ShopUserInterface $user,
        Request $request,
        TokenInterface $token,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($order);
        $token->getUser()->willReturn($user);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('onInteractiveLogin', [new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject())])
        ;
    }

    function it_blames_cart_on_user_on_implicit_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        UserEvent $userEvent,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($cart);
        $cart->getCustomer()->willReturn(null);
        $userEvent->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomerWithAuthorization($customer)->shouldBeCalled();

        $this->onImplicitLogin($userEvent);
    }

    function it_blames_cart_on_user_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($cart);
        $cart->getCustomer()->willReturn(null);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomerWithAuthorization($customer)->shouldBeCalled();

        $this->onInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_given_cart_has_been_blamed_in_past(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        CustomerInterface $customer,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($cart);
        $cart->getCustomer()->willReturn($customer);

        $cart->setCustomerWithAuthorization(Argument::any())->shouldNotBeCalled();

        $this->onInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_given_user_is_invalid_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($cart);
        $token->getUser()->willReturn('anon.');

        $cart->setCustomerWithAuthorization(Argument::any())->shouldNotBeCalled();

        $this->onInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_implicit_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        UserEvent $userEvent,
        ShopUserInterface $user,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $userEvent->getUser()->willReturn($user);

        $this->onImplicitLogin($userEvent);
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        Request $request,
        TokenInterface $token,
        ShopUserInterface $user,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $token->getUser()->willReturn($user);

        $this->onInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_the_current_section_is_not_shop_on_implicit_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        UserEvent $userEvent,
        SectionInterface $section,
    ): void {
        $sectionResolver->getSection()->willReturn($section);

        $userEvent->getUser()->shouldNotBeCalled();
        $cartContext->getCart()->shouldNotBeCalled();

        $this->onImplicitLogin($userEvent);
    }

    function it_does_nothing_if_the_current_section_is_not_shop_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        Request $request,
        TokenInterface $token,
        SectionInterface $section,
    ): void {
        $sectionResolver->getSection()->willReturn($section);

        $token->getUser()->shouldNotBeCalled();
        $cartContext->getCart()->shouldNotBeCalled();

        $this->onInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }
}
