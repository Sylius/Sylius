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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

final class ShipmentSpec extends ObjectBehavior
{
    function it_implements_a_shipment_interface(): void
    {
        $this->shouldImplement(ShipmentInterface::class);
    }

    function it_extends_a_base_shipment(): void
    {
        $this->shouldHaveType(BaseShipment::class);
    }

    function it_does_not_belong_to_an_order_by_default(): void
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_allows_attaching_itself_to_an_order(OrderInterface $order): void
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    function it_allows_detaching_itself_from_an_order(OrderInterface $order): void
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }
}
