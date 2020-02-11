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

namespace spec\Sylius\Bundle\ShippingBundle\Assigner;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ShippingDateAssignerSpec extends ObjectBehavior
{
    function it_implements_a_shipping_date_assigner_interface(): void
    {
        $this->shouldImplement(ShippingDateAssignerInterface::class);
    }

    function let(DateTimeProvider $calendar): void
    {
        $this->beConstructedWith($calendar);
    }

    function it_assigns_a_shipped_at_date_to_a_shipment(ShipmentInterface $shipment, DateTimeProvider $calendar): void
    {
        $calendar->today()->willReturn(new \DateTime('20-05-2019 20:20:20'));
        $shipment->setShippedAt(new \DateTime('20-05-2019 20:20:20'))->shouldBeCalled();

        $this->assign($shipment);
    }
}
