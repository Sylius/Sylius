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
use Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UserCartRecalculationListenerSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor)
    {
        $this->beConstructedWith($cartContext, $orderProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserCartRecalculationListener::class);
    }

    function it_recalculates_cart_for_logged_in_user(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        Event $event,
        OrderInterface $order
    ) {
        $cartContext->getCart()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateCartWhileLogin($event);
    }

    function it_does_nothing_if_cannot_find_cart(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        Event $event
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $orderProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->recalculateCartWhileLogin($event);
    }
}
