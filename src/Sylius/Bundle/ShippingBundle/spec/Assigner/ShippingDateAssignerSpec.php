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
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Clock\ClockInterface;

final class ShippingDateAssignerSpec extends ObjectBehavior
{
    function let(ClockInterface $clock): void
    {
        $this->beConstructedWith($clock);
    }

    function it_implements_a_shipping_date_assigner_interface(): void
    {
        $this->shouldImplement(ShippingDateAssignerInterface::class);
    }

    function it_assigns_a_shipped_at_date_to_a_shipment(
        ClockInterface $clock,
        ShipmentInterface $shipment,
    ): void {
        $clock->now()->willReturn(new \DateTimeImmutable('20-05-2019 20:20:20'));
        $shipment->setShippedAt(new \DateTimeImmutable('20-05-2019 20:20:20'))->shouldBeCalled();

        $this->assign($shipment);
    }
}
