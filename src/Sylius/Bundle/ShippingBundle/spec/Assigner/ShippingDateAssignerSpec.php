<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShippingBundle\Assigner;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ShippingDateAssignerSpec extends ObjectBehavior
{
    function it_implements_a_shipping_date_assigner_interface(DateTimeProviderInterface $calendar): void
    {
        $this->beConstructedWith($calendar);

        $this->shouldImplement(ShippingDateAssignerInterface::class);
    }

    function it_assigns_a_shipped_at_date_to_a_shipment_using_date_time_provider_interface_from_sylius_calendar_repository(
        DateTimeProviderInterface $calendar,
        ShipmentInterface $shipment,
    ): void {
        $this->beConstructedWith($calendar);

        $calendar->now()->willReturn(new \DateTime('20-05-2019 20:20:20'));
        $shipment->setShippedAt(new \DateTime('20-05-2019 20:20:20'))->shouldBeCalled();

        $this->assign($shipment);
    }

    function it_assigns_a_shipped_at_date_to_a_shipment_using_deprecated_date_time_provider(
        DateTimeProvider $calendar,
        ShipmentInterface $shipment,
    ): void {
        $this->beConstructedWith($calendar);

        $calendar->today()->willReturn(new \DateTime('20-05-2019 20:20:20'));
        $shipment->setShippedAt(new \DateTime('20-05-2019 20:20:20'))->shouldBeCalled();

        $this->assign($shipment);
    }
}
