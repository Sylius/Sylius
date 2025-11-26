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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Adjustment;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class AdjustmentTest extends TestCase
{
    private Adjustment $adjustment;

    protected function setUp(): void
    {
        $this->adjustment = new Adjustment();
    }

    public function testShouldImplementAdjustmentInterface(): void
    {
        $this->assertInstanceOf(AdjustmentInterface::class, $this->adjustment);
    }

    public function testShouldAllowAssignItselfToShipment(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $shipment->expects($this->once())->method('getOrder')->willReturn($order);

        $this->adjustment->setShipment($shipment);

        $this->assertEquals($shipment, $this->adjustment->getShipment());
    }
}
