<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\Shipment;

final class ShipmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Shipment');
    }

    function it_should_implement_Sylius_core_shipment_interface()
    {
        $this->shouldImplement(ShipmentInterface::class);
    }

    function it_should_extend_Sylius_shipment_mapped_superclass()
    {
        $this->shouldHaveType(Shipment::class);
    }

    function it_should_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_should_allow_attaching_itself_to_an_order(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    function it_should_allow_detaching_itself_from_an_order(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }
}
