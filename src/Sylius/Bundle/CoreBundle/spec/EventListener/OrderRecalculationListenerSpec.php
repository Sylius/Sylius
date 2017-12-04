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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderRecalculationListenerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_uses_order_processor_to_recalculate_order(
        OrderProcessorInterface $orderProcessor,
        GenericEvent $event,
        OrderInterface $order
    ): void {
        $event->getSubject()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateOrder($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(GenericEvent $event): void
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('recalculateOrder', [$event])
        ;
    }
}
