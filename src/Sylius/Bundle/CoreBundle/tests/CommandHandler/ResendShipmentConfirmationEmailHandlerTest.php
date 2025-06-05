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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\ResendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\ResendShipmentConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class ResendShipmentConfirmationEmailHandlerTest extends TestCase
{
    private MockObject&RepositoryInterface $shipmentRepository;

    private MockObject&ShipmentEmailManagerInterface $shipmentEmailManager;

    private ResendShipmentConfirmationEmailHandler $handler;

    protected function setUp(): void
    {
        $this->shipmentRepository = $this->createMock(RepositoryInterface::class);
        $this->shipmentEmailManager = $this->createMock(ShipmentEmailManagerInterface::class);

        $this->handler = new ResendShipmentConfirmationEmailHandler(
            $this->shipmentRepository,
            $this->shipmentEmailManager,
        );
    }

    public function testItIsAMessageHandler(): void
    {
        $reflection = new \ReflectionClass(ResendShipmentConfirmationEmailHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testItResendsShipmentConfirmationEmail(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->shipmentRepository
            ->method('find')
            ->with('12')
            ->willReturn($shipment)
        ;

        $this->shipmentEmailManager
            ->expects($this->once())
            ->method('resendConfirmationEmail')
            ->with($shipment)
        ;

        ($this->handler)(new ResendShipmentConfirmationEmail(12));
    }

    public function testItThrowsNotFoundExceptionWhenShipmentNotFound(): void
    {
        $this->shipmentRepository
            ->method('find')
            ->with('10')
            ->willReturn(null)
        ;

        $this->expectException(NotFoundHttpException::class);

        ($this->handler)(new ResendShipmentConfirmationEmail(10));
    }
}
