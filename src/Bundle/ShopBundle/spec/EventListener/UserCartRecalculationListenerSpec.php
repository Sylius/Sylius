<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class UserCartRecalculationListenerSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
    ): void {
        $this->beConstructedWith($cartContext, $orderProcessor, $uriBasedSectionContext);
    }

    function it_recalculates_cart_for_logged_in_user_and_interactive_login_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
        Request $request,
        TokenInterface $token,
        OrderInterface $order,
        ShopSection $shopSection,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_recalculates_cart_for_logged_in_user_and_user_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
        OrderInterface $order,
        ShopSection $shopSection,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_cannot_find_cart_for_interactive_login_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
        Request $request,
        TokenInterface $token,
        ShopSection $shopSection,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_cannot_find_cart_for_user_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
        ShopSection $shopSection,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($shopSection);
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_section_is_different_than_shop_section(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn(null);
        $cartContext->getCart()->shouldNotBeCalled();
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }
}
