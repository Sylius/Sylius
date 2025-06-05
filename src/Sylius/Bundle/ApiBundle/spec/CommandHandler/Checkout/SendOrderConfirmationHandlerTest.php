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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class SendOrderConfirmationHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var OrderEmailManagerInterface|MockObject */
    private MockObject $orderEmailManagerMock;

    private SendOrderConfirmationHandler $sendOrderConfirmationHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderEmailManagerMock = $this->createMock(OrderEmailManagerInterface::class);
        $this->sendOrderConfirmationHandler = new SendOrderConfirmationHandler($this->orderRepositoryMock, $this->orderEmailManagerMock);
    }

    public function testSendsOrderConfirmationMessage(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->orderRepositoryMock->expects(self::once())->method('findOneByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getLocaleCode')->willReturn('pl_PL');
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $customerMock->expects(self::once())->method('getEmail')->willReturn('johnny.bravo@email.com');
        $this->orderEmailManagerMock->expects(self::once())->method('sendConfirmationEmail')->with($orderMock);
        $this(new SendOrderConfirmation('TOKEN'));
    }
}
