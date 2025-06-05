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

namespace Tests\Sylius\Bundle\CoreBundle\CommandDispatcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\ResendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcher;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResendShipmentConfirmationEmailDispatcherTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;

    private ResendShipmentConfirmationEmailDispatcher $resendShipmentConfirmationEmailDispatcher;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->resendShipmentConfirmationEmailDispatcher = new ResendShipmentConfirmationEmailDispatcher($this->messageBus);
    }

    public function testDispatchesAResendConfirmationEmail(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);

        $shipment->expects($this->once())->method('getId')->willReturn(12);

        $message = new ResendShipmentConfirmationEmail(12);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->resendShipmentConfirmationEmailDispatcher->dispatch($shipment);
    }
}
