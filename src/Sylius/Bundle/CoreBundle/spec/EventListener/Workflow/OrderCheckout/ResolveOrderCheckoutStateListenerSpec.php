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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderCheckoutStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $orderCheckoutStateResolver): void
    {
        $this->beConstructedWith($orderCheckoutStateResolver);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $callback): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($callback->getWrappedObject(), new Marking())]);
    }

    function it_resolves_order_checkout_state_after_select_shipping(
        StateResolverInterface $orderCheckoutStateResolver,
        OrderInterface $order,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());

        $this($event);

        $orderCheckoutStateResolver->resolve($order)->shouldHaveBeenCalledOnce();
    }
}
