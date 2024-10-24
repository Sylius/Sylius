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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class AdjustmentSpec extends ObjectBehavior
{
    function it_implements_an_adjustment_interface(): void
    {
        $this->shouldImplement(AdjustmentInterface::class);
    }

    function it_allows_assigning_itself_to_a_shipment(ShipmentInterface $shipment, OrderInterface $order): void
    {
        $shipment->getOrder()->willReturn($order);

        $this->setShipment($shipment);

        $this->getShipment()->shouldReturn($shipment);
    }
}
