<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;

class ShipmentSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Shipment');
    }

    function it_should_implement_Sylius_core_shipment_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\ShipmentInterface');
    }

    function it_should_extend_Sylius_shipment_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\Shipment');
    }

    function it_should_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_should_allow_attaching_itself_to_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_should_allow_detaching_itself_from_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }
}
