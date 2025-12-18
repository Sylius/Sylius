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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangePaymentMethodHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ChangePaymentMethodHandlerTest extends TestCase
{
    private MockObject&PaymentMethodChangerInterface $paymentMethodChanger;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private ChangePaymentMethodHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentMethodChanger = $this->createMock(PaymentMethodChangerInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->handler = new ChangePaymentMethodHandler($this->paymentMethodChanger, $this->orderRepository);
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        $changePaymentMethod = new ChangePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn(null);
        $this->paymentMethodChanger->expects(self::never())
            ->method('changePaymentMethod')
            ->with('CASH_ON_DELIVERY_METHOD', 123, $this
                ->isInstanceOf(OrderInterface::class));
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($changePaymentMethod);
    }

    public function testAssignsShopUserSChangePaymentMethodToSpecifiedPaymentAfterCheckoutCompleted(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $changePaymentMethod = new ChangePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($order);
        $this->paymentMethodChanger->expects(self::once())
            ->method('changePaymentMethod')
            ->with('CASH_ON_DELIVERY_METHOD', 123, $order)
            ->willReturn($order);
        self::assertSame($order, $this->handler->__invoke($changePaymentMethod));
    }
}
