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
use Sylius\Bundle\CoreBundle\EventListener\OrderRecalculationListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderRecalculationListenerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor)
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderRecalculationListener::class);
    }

    function it_uses_order_processor_to_recalculate_order(
        OrderProcessorInterface $orderProcessor,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateOrder($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(GenericEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('recalculateOrder', [$event])
        ;
    }
}
