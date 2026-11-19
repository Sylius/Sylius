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

namespace Sylius\Tests\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Adjustment;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Shipment;

final class OrderShippingAdjustmentRemovalTest extends TestCase
{
    #[Test]
    public function it_clears_shipment_reference_when_shipping_adjustment_is_removed_from_order(): void
    {
        $order = new Order();
        $shipment = new Shipment();
        $shipment->setOrder($order);

        $adjustment = new Adjustment();
        $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $adjustment->setAmount(500);

        $shipment->addAdjustment($adjustment);

        $this->assertSame($order, $adjustment->getAdjustable(), 'Adjustment should be assigned to order after addAdjustment on shipment');
        $this->assertSame($shipment, $adjustment->getShipment(), 'Adjustment should reference the shipment');
        $this->assertTrue($order->hasAdjustment($adjustment), 'Order should contain the adjustment');
        $this->assertTrue($shipment->hasAdjustment($adjustment), 'Shipment should contain the adjustment');

        $order->removeAdjustment($adjustment);

        $this->assertNull($adjustment->getAdjustable(), 'Adjustable reference should be cleared');
        $this->assertNull($adjustment->getShipment(), 'Shipment reference should be cleared after removal from order');
        $this->assertFalse($order->hasAdjustment($adjustment), 'Order should not contain the adjustment');
        $this->assertFalse($shipment->hasAdjustment($adjustment), 'Shipment should not contain the adjustment after removal from order');
    }
}
