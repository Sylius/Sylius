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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingListenerSpec extends ObjectBehavior
{
    function let(
        OrderShipmentProcessorInterface $orderShipmentProcessor,
        ShipmentProcessorInterface $shippingProcessor
    ) {
        $this->beConstructedWith($orderShipmentProcessor, $shippingProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderShippingListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(
        GenericEvent $event,
        \stdClass $invalidSubject
    ) {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('processOrderShipments', [$event])
        ;
    }

    function it_calls_shipping_processor_on_order(
        OrderShipmentProcessorInterface $orderShipmentProcessor,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $orderShipmentProcessor->processOrderShipment($order)->shouldBeCalled();

        $this->processOrderShipments($event);
    }
}
