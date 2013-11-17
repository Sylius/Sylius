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
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactoryInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\ShippingChargesProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderShippingListenerSpec extends ObjectBehavior
{
    function let(ShipmentFactoryInterface $shipmentFactory, ShippingChargesProcessorInterface $shippingProcessor)
    {
        $this->beConstructedWith($shipmentFactory, $shippingProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderShippingListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessOrderShippingCharges($event)
        ;
    }

    function it_calls_shipping_processor_on_order($shippingProcessor, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $shippingProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->processOrderShippingCharges($event);
    }
}
