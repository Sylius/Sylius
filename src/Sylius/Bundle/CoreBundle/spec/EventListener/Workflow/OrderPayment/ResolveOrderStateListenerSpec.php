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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment;

use PhpSpec\ObjectBehavior;
use stdClass;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $orderStateResolver): void
    {
        $this->beConstructedWith($orderStateResolver);
    }

    function it_throws_an_exception_on_non_supported_subject(stdClass $subject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($subject->getWrappedObject(), new Marking())])
        ;
    }

    function it_resolves_order_state(
        StateResolverInterface $orderStateResolver,
        OrderInterface $order,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());

        $this($event);

        $orderStateResolver->resolve($order)->shouldHaveBeenCalledOnce();
    }
}
