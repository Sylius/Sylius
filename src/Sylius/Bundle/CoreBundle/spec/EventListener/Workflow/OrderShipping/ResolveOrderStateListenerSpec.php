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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $stateResolver): void
    {
        $this->beConstructedWith($stateResolver);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $callback): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($callback->getWrappedObject(), new Marking())])
        ;
    }

    function it_resolves_order_state_after_order_being_shipped(
        StateResolverInterface $stateResolver,
    ): void {
        $order = new Order();
        $event = new CompletedEvent($order, new Marking());

        $stateResolver->resolve($order)->shouldBeCalled();

        $this($event);
    }
}
