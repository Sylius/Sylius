<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShippingContextSpec extends ObjectBehavior
{
    function let(ShipmentRepositoryInterface $shippingRepository)
    {
        $this->beConstructedWith($shippingRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Domain\ShippingContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_an_shipment_exists_in_repository(
        ShipmentRepositoryInterface $shippingRepository,
        ShippingMethodInterface $freeDeliveryShipmentMethod
    ) {
        $shippingRepository->findBy(['method' => $freeDeliveryShipmentMethod])->willReturn([]);

        $this->shipmentShouldNotExistInTheRegistry($freeDeliveryShipmentMethod);
    }

    function it_throws_an_exception_if_shipment_still_exist(
        ShipmentRepositoryInterface $shippingRepository,
        ShippingMethodInterface $dhlShipmentMethod,
        ShipmentInterface $shipment
    ) {
        $shippingRepository->findBy(['method' => $dhlShipmentMethod])->willReturn([$shipment]);

        $this->shouldThrow(NotEqualException::class)->during('shipmentShouldNotExistInTheRegistry', [$dhlShipmentMethod]);
    }
}
