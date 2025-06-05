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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangePaymentMethodHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class ChangePaymentMethodHandlerTest extends TestCase
{
    /** @var PaymentMethodChangerInterface|MockObject */
    private MockObject $paymentMethodChangerMock;

    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    private ChangePaymentMethodHandler $changePaymentMethodHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->paymentMethodChangerMock = $this->createMock(PaymentMethodChangerInterface::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->changePaymentMethodHandler = new ChangePaymentMethodHandler($this->paymentMethodChangerMock, $this->orderRepositoryMock);
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        $changePaymentMethod = new ChangePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);
        $this->paymentMethodChangerMock->expects(self::never())->method('changePaymentMethod')->with('CASH_ON_DELIVERY_METHOD', 123, $this->isInstanceOf(OrderInterface::class))
        ;
        $this->expectException(InvalidArgumentException::class);
        $this->changePaymentMethodHandler->__invoke($changePaymentMethod);
    }

    public function testAssignsShopUserSChangePaymentMethodToSpecifiedPaymentAfterCheckoutCompleted(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $changePaymentMethod = new ChangePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $this->paymentMethodChangerMock->expects(self::once())->method('changePaymentMethod')->with('CASH_ON_DELIVERY_METHOD', 123, $orderMock)
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($changePaymentMethod));
    }
}
