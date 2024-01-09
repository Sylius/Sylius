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

namespace Sylius\Bundle\CoreBundle\MessageDispatcher;

use Sylius\Bundle\CoreBundle\Message\Admin\ResendShipmentConfirmationEmail;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResendShipmentConfirmationEmailDispatcher implements ResendShipmentConfirmationEmailDispatcherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(int|string $shipmentId): void
    {
        $this->messageBus->dispatch(new ResendShipmentConfirmationEmail($shipmentId));
    }
}
