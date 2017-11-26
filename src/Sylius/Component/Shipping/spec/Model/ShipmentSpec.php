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
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class ShipmentSpec extends ObjectBehavior
{
    public function it_implements_shipment_interface(): void
    {
        $this->shouldImplement(ShipmentInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_ready_state_by_default(): void
    {
        $this->getState()->shouldReturn(ShipmentInterface::STATE_CART);
    }

    public function its_state_is_mutable(): void
    {
        $this->setState(ShipmentInterface::STATE_SHIPPED);
        $this->getState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

    public function it_has_no_shipping_method_by_default(): void
    {
        $this->getMethod()->shouldReturn(null);
    }

    public function its_shipping_method_is_mutable(ShippingMethodInterface $shippingMethod): void
    {
        $this->setMethod($shippingMethod);
        $this->getMethod()->shouldReturn($shippingMethod);
    }

    public function it_initializes_units_collection_by_default(): void
    {
        $this->getUnits()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_adds_units(ShipmentUnitInterface $shipmentUnit): void
    {
        $this->hasUnit($shipmentUnit)->shouldReturn(false);

        $shipmentUnit->setShipment($this)->shouldBeCalled();
        $this->addUnit($shipmentUnit);

        $this->hasUnit($shipmentUnit)->shouldReturn(true);
    }

    public function it_removes_unit(ShipmentUnitInterface $shipmentUnit): void
    {
        $this->hasUnit($shipmentUnit)->shouldReturn(false);

        $shipmentUnit->setShipment($this)->shouldBeCalled();
        $this->addUnit($shipmentUnit);

        $shipmentUnit->setShipment(null)->shouldBeCalled();
        $this->removeUnit($shipmentUnit);

        $this->hasUnit($shipmentUnit)->shouldReturn(false);
    }

    public function it_has_no_tracking_code_by_default(): void
    {
        $this->getTracking()->shouldReturn(null);
    }

    public function its_tracking_code_is_mutable(): void
    {
        $this->setTracking('5346172074');
        $this->getTracking()->shouldReturn('5346172074');
    }

    public function it_is_not_tracked_by_default(): void
    {
        $this->shouldNotBeTracked();
    }

    public function it_is_tracked_only_if_tracking_code_is_defined(): void
    {
        $this->shouldNotBeTracked();
        $this->setTracking('5346172074');
        $this->shouldBeTracked();
        $this->setTracking(null);
        $this->shouldNotBeTracked();
    }

    public function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
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
