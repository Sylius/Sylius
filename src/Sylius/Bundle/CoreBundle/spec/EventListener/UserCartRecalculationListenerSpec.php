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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserCartRecalculationListenerSpec extends ObjectBehavior
{
    function let(CartProviderInterface $cartProvider, OrderRecalculatorInterface $orderRecalculator)
    {
        $this->beConstructedWith($cartProvider, $orderRecalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener');
    }

    function it_recalculates_logged_in_user_cart(
        CartProviderInterface $cartProvider,
        OrderInterface $order,
        OrderRecalculatorInterface $orderRecalculator,
        UserEvent $event
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($order);
        $orderRecalculator->recalculate($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_there_is_no_cart_while_login(
        CartProviderInterface $cartProvider,
        UserEvent $event
    ) {
        $cartProvider->hasCart()->willReturn(false);
        $cartProvider->getCart()->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_recalculates_interactively_logged_in_user_cart(
        CartProviderInterface $cartProvider,
        InteractiveLoginEvent $event,
        OrderInterface $order,
        OrderRecalculatorInterface $orderRecalculator
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($order);
        $orderRecalculator->recalculate($order)->shouldBeCalled();

        $this->recalculateCartWhileInteractiveLogin($event);
    }

    function it_does_nothing_if_there_is_no_cart_while_interactive_login(
        CartProviderInterface $cartProvider,
        InteractiveLoginEvent $event
    ) {
        $cartProvider->hasCart()->willReturn(false);
        $cartProvider->getCart()->shouldNotBeCalled();

        $this->recalculateCartWhileInteractiveLogin($event);
    }

    function it_throws_exception_if_provided_cart_is_not_order(
        CartInterface $cart,
        CartProviderInterface $cartProvider,
        InteractiveLoginEvent $interactiveLoginEvent,
        UserEvent $userEvent
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart, $cart);

        $this
            ->shouldThrow(new UnexpectedTypeException($cart->getWrappedObject(), OrderInterface::class))
            ->during('recalculateCartWhileLogin', [$userEvent])
        ;

        $this
            ->shouldThrow(new UnexpectedTypeException($cart->getWrappedObject(), OrderInterface::class))
            ->during('recalculateCartWhileInteractiveLogin', [$interactiveLoginEvent])
        ;
    }
}
