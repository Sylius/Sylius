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
use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcher;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResendOrderConfirmationEmailDispatcherTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;

    private ResendOrderConfirmationEmailDispatcher $resendOrderConfirmationEmailDispatcher;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->resendOrderConfirmationEmailDispatcher = new ResendOrderConfirmationEmailDispatcher($this->messageBus);
    }

    public function testDispatchesAResendConfirmationEmail(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order->expects($this->once())->method('getTokenValue')->willReturn('token');

        $message = new ResendOrderConfirmationEmail('token');

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message))
        ;

        $this->resendOrderConfirmationEmailDispatcher->dispatch($order);
    }
}
