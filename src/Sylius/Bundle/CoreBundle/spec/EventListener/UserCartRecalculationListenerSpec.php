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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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
    function let(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($cartContext, $orderProcessor);
    }

    function it_recalculates_cart_for_logged_in_user_and_interactive_login_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        Request $request,
        TokenInterface $token,
        OrderInterface $order
    ): void {
        $cartContext->getCart()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_recalculates_cart_for_logged_in_user_and_user_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        UserEvent $event,
        OrderInterface $order
    ): void {
        $cartContext->getCart()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_cannot_find_cart_for_interactive_login_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        Request $request,
        TokenInterface $token
    ): void {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_cannot_find_cart_for_user_event(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        UserEvent $event
    ): void {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }
}
