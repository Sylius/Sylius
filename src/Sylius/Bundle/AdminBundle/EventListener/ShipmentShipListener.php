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

namespace Sylius\Bundle\AdminBundle\EventListener;

use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface as DeprecatedShipmentEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class ShipmentShipListener
{
    public function __construct(private DeprecatedShipmentEmailManagerInterface|ShipmentEmailManagerInterface $shipmentEmailManager)
    {
        if ($this->shipmentEmailManager instanceof DeprecatedShipmentEmailManagerInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                DeprecatedShipmentEmailManagerInterface::class,
                self::class,
                ShipmentEmailManagerInterface::class,
            );
        }
    }

    public function sendConfirmationEmail(GenericEvent $event): void
    {
        $shipment = $event->getSubject();
        Assert::isInstanceOf($shipment, ShipmentInterface::class);

        $this->shipmentEmailManager->sendConfirmationEmail($shipment);
    }
}
