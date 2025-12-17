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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class SendShipmentConfirmationEmailHandlerTest extends TestCase
{
    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&ShipmentEmailManagerInterface $shipmentEmailManager;

    private SendShipmentConfirmationEmailHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->shipmentEmailManager = $this->createMock(ShipmentEmailManagerInterface::class);
        $this->handler = new SendShipmentConfirmationEmailHandler(
            $this->shipmentRepository,
            $this->shipmentEmailManager,
        );
    }

    public function testSendsShipmentConfirmationMessage(): void
    {
        /** @var ShipmentInterface|MockObject $shipment */
        $shipment = $this->createMock(ShipmentInterface::class);
        /** @var CustomerInterface|MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->shipmentRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($shipment);
        $shipment->expects(self::once())->method('getOrder')->willReturn($orderMock);
        $orderMock->method('getChannel')->willReturn($channel);
        $orderMock->method('getLocaleCode')->willReturn('pl_PL');
        $orderMock->method('getCustomer')->willReturn($customer);
        $customer->expects(self::once())->method('getEmail')->willReturn('johnny.bravo@email.com');
        $this->shipmentEmailManager->expects(self::once())->method('sendConfirmationEmail')->with($shipment);
        $this->handler->__invoke(new SendShipmentConfirmationEmail(123));
    }
}
