<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentsFactorySpec extends ObjectBehavior
{
    function let(ResourceFactoryInterface $shipmentFactory)
    {
        $this->beConstructedWith($shipmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderShipmentsFactory');
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\OrderShipmentsFactoryInterface');
    }
}
