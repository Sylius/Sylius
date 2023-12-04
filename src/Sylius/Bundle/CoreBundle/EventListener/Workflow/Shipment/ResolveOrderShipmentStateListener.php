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

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class ResolveOrderShipmentStateListener
{
    public function __construct(private StateResolverInterface $orderStateResolver)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        $shipment = $event->getSubject();
        Assert::isInstanceOf($shipment, ShipmentInterface::class);

        $order = $shipment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderStateResolver->resolve($order);
    }
}
