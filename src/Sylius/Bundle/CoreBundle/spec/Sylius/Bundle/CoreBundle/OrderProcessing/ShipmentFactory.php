<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use PHPSpec2\ObjectBehavior;

/**
 * Shipment factory spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentFactory extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $shipmentRepository
     */
    function let($shipmentRepository)
    {
        $this->beConstructedWith($shipmentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactory');
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactoryInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface              $order
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface   $inventoryUnit
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_creates_a_single_shipment_and_assigns_all_inventory_units_to_it($shipmentRepository, $order, $shipment, $inventoryUnit, $method)
    {
        $shipmentRepository->createNew()->willReturn($shipment);
        $order->getInventoryUnits()->willReturn(array($inventoryUnit));
        $shipment->addItem($inventoryUnit)->shouldBeCalled();
        $shipment->setMethod($method)->shouldBeCalled();
        $order->addShipment($shipment)->shouldBeCalled();

        $this->createShipment($order, $method);
    }
}
