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
use Symfony\Component\EventDispatcher\GenericEvent;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\ShipmentFactoryInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingListenerSpec extends ObjectBehavior
{
    public function let(
        ShipmentFactoryInterface $shipmentFactory,
        ShipmentProcessorInterface $shippingProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->beConstructedWith($shipmentFactory, $shippingProcessor, $shippingChargesProcessor);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderShippingListener');
    }

    public function it_throws_exception_if_event_has_non_order_subject(
        GenericEvent $event,
        \stdClass $invalidSubject
    ) {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessOrderShippingCharges($event)
        ;
    }

    public function it_calls_shipping_processor_on_order(
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->processOrderShippingCharges($event);
    }
}
