<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

final class ShipmentUnitSpec extends ObjectBehavior
{
    public function it_implements_shipment_unit_interface(): void
    {
        $this->shouldImplement(ShipmentUnitInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_does_not_belong_to_shipment_by_default(): void
    {
        $this->getShipment()->shouldReturn(null);
    }

    public function it_allows_assigning_itself_to_shipment(ShipmentInterface $shipment): void
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    public function it_allows_detaching_itself_from_shipment(ShipmentInterface $shipment): void
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);

        $this->setShipment(null);
        $this->getShipment()->shouldReturn(null);
    }

    public function it_has_no_shippable_defined_by_default(): void
    {
        $this->getShippable()->shouldReturn(null);
    }

    public function it_allows_defining_shippable(ShippableInterface $shippable): void
    {
        $this->setShippable($shippable);
        $this->getShippable()->shouldReturn($shippable);
    }

    public function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    public function its_creation_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
