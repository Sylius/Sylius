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

namespace Tests\Sylius\Bundle\ShippingBundle\Assigner;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssigner;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Clock\ClockInterface;

final class ShippingDateAssignerTest extends TestCase
{
    private ClockInterface&MockObject $clock;

    private ShippingDateAssigner $shippingDateAssigner;

    protected function setUp(): void
    {
        $this->clock = $this->createMock(ClockInterface::class);
        $this->shippingDateAssigner = new ShippingDateAssigner($this->clock);
    }

    public function testImplementsAShippingDateAssignerInterface(): void
    {
        $this->assertInstanceOf(ShippingDateAssignerInterface::class, $this->shippingDateAssigner);
    }

    public function testAssignsAShippedAtDateToAShipment(): void
    {
        /** @var ShipmentInterface&MockObject $shipment */
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->clock->expects($this->once())->method('now')->willReturn(new DateTimeImmutable('20-05-2019 20:20:20'));
        $shipment->expects($this->once())->method('setShippedAt')->with(new DateTimeImmutable('20-05-2019 20:20:20'));

        $this->shippingDateAssigner->assign($shipment);
    }
}
