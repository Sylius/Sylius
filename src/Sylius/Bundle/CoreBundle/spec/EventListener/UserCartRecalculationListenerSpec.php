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
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserCartRecalculationListenerSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, OrderRecalculatorInterface $orderRecalculator)
    {
        $this->beConstructedWith($cartContext, $orderRecalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener');
    }

    function it_recalculates_cart_for_logged_in_user(
        CartContextInterface $cartContext,
        Event $event,
        OrderInterface $order,
        OrderRecalculatorInterface $orderRecalculator
    ) {
        $cartContext->getCart()->willReturn($order);
        $orderRecalculator->recalculate($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_throws_exception_if_provided_cart_is_not_order(
        CartInterface $cart,
        CartContextInterface $cartContext,
        Event $event
    ) {
        $cartContext->getCart()->willReturn($cart);

        $this
            ->shouldThrow(new UnexpectedTypeException($cart->getWrappedObject(), OrderInterface::class))
            ->during('recalculateCartWhileLogin', [$event])
        ;
    }
}
