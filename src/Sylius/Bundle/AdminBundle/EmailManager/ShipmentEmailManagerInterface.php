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

namespace Sylius\Bundle\AdminBundle\EmailManager;

use Sylius\Component\Core\Model\ShipmentInterface;

trigger_deprecation(
    'sylius/admin-bundle',
    '1.13',
    'The "%s" interface is deprecated, use "%s" instead.',
    ShipmentEmailManagerInterface::class,
    \Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface} instead. */
interface ShipmentEmailManagerInterface
{
    public function sendConfirmationEmail(ShipmentInterface $shipment): void;
}
