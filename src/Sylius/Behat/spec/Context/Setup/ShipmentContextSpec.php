<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ShipmentContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $shipmentRepository,
        FactoryInterface $shipmentFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->beConstructedWith($shipmentRepository, $shipmentFactory, $sharedStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ShipmentContext');
    }

    function it_implements_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_shipment_from_stored_shipping_method(
        RepositoryInterface $shipmentRepository,
        FactoryInterface $shipmentFactory,
        SharedStorageInterface $sharedStorage,
        ShippingMethodInterface $shippingMethod,
        OrderInterface $order,
        ShipmentInterface $shipment
    ) {
        $sharedStorage->getCurrentResource('shippingMethod')->willReturn($shippingMethod);
        $sharedStorage->getCurrentResource('order')->willReturn($order);
        $shipmentFactory->createNew()->willReturn($shipment);

        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $shipment->setOrder($order)->shouldBeCalled();
        $shipment->setState(ShipmentInterface::STATE_SHIPPED)->shouldBeCalled();
        $shipment->setTracking('')->shouldBeCalled();

        $shipmentRepository->add($shipment)->shouldBeCalled();

        $this->thereIsShipmentUsingShippingMethod();
    }
}
