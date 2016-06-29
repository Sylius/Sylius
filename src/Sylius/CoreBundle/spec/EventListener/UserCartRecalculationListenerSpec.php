<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Cart\Model\CartInterface;
use Sylius\Cart\Provider\CartProviderInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;

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
        $this->shouldHaveType('Sylius\CoreBundle\EventListener\UserCartRecalculationListener');
    }

    function it_recalculates_cart_for_logged_in_user(
        CartProviderInterface $cartProvider,
        Event $event,
        OrderInterface $order,
        OrderRecalculatorInterface $orderRecalculator
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($order);
        $orderRecalculator->recalculate($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_there_is_no_cart_while_login(
        CartProviderInterface $cartProvider,
        Event $event
    ) {
        $cartProvider->hasCart()->willReturn(false);
        $cartProvider->getCart()->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_throws_exception_if_provided_cart_is_not_order(
        CartInterface $cart,
        CartProviderInterface $cartProvider,
        Event $event
    ) {
        $cartProvider->hasCart()->willReturn(true);
        $cartProvider->getCart()->willReturn($cart);

        $this
            ->shouldThrow(new UnexpectedTypeException($cart->getWrappedObject(), OrderInterface::class))
            ->during('recalculateCartWhileLogin', [$event])
        ;
    }
}
