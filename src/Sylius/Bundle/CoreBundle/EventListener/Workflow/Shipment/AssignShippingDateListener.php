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

use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Webmozart\Assert\Assert;

final class AssignShippingDateListener
{
    public function __construct(private ShippingDateAssignerInterface $shippingDateAssigner)
    {
    }

    public function __invoke(TransitionEvent $event): void
    {
        $shipment = $event->getSubject();
        Assert::isInstanceOf($shipment, ShipmentInterface::class);

        $this->shippingDateAssigner->assign($shipment);
    }
}
