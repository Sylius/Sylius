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

namespace Tests\Sylius\Bundle\AdminBundle\EventListener;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ShipmentShipListenerTest extends TestCase
{
    private MockObject&ShipmentEmailManagerInterface $shipmentEmailManagerMock;

    private ShipmentShipListener $shipmentShipListener;

    protected function setUp(): void
    {
        $this->shipmentEmailManagerMock = $this->createMock(ShipmentEmailManagerInterface::class);
        $this->shipmentShipListener = new ShipmentShipListener($this->shipmentEmailManagerMock);
    }

    public function testSendsAConfirmationEmail(): void
    {
        $eventMock = $this->createMock(GenericEvent::class);
        $shipmentMock = $this->createMock(ShipmentInterface::class);

        $eventMock->expects($this->once())->method('getSubject')->willReturn($shipmentMock);

        $this->shipmentEmailManagerMock
            ->expects($this->once())
            ->method('sendConfirmationEmail')
            ->with($shipmentMock)
        ;

        $this->shipmentShipListener->sendConfirmationEmail($eventMock);
    }

    public function testThrowsAnInvalidArgumentExceptionIfAnEventSubjectIsNotAShipmentInstance(): void
    {
        $eventMock = $this->createMock(GenericEvent::class);
        $shipmentMock = $this->createMock(stdClass::class);

        $eventMock->expects($this->once())->method('getSubject')->willReturn($shipmentMock);

        $this->expectException(InvalidArgumentException::class);
        $this->shipmentShipListener->sendConfirmationEmail($eventMock);
    }
}
