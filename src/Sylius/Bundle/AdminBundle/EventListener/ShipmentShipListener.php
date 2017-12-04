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

namespace Sylius\Bundle\AdminBundle\EventListener;

use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class ShipmentShipListener
{
    /**
     * @var ShipmentEmailManagerInterface
     */
    private $shipmentEmailManager;

    /**
     * @param ShipmentEmailManagerInterface $shipmentEmailManager
     */
    public function __construct(ShipmentEmailManagerInterface $shipmentEmailManager)
    {
        $this->shipmentEmailManager = $shipmentEmailManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendConfirmationEmail(GenericEvent $event): void
    {
        $shipment = $event->getSubject();
        Assert::isInstanceOf($shipment, ShipmentInterface::class);

        $this->shipmentEmailManager->sendConfirmationEmail($shipment);
    }
}
