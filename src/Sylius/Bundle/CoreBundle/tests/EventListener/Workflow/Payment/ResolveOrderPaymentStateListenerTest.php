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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ResolveOrderPaymentStateListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderPaymentStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $stateResolver;

    private ResolveOrderPaymentStateListener $resolveOrderPaymentStateListener;

    protected function setUp(): void
    {
        $this->stateResolver = $this->createMock(StateResolverInterface::class);
        $this->resolveOrderPaymentStateListener = new ResolveOrderPaymentStateListener($this->stateResolver);
    }

    public function testThrowsExceptionWhenEventSubjectIsNotAPayment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->resolveOrderPaymentStateListener->__invoke(new CompletedEvent(new stdClass(), new Marking()));
    }

    public function testThrowsExceptionWhenEventPaymentHasNoOrder(): void
    {
        $payment = $this->createMock(PaymentInterface::class);

        $payment->expects($this->once())->method('getOrder')->willReturn(null);

        $this->expectException(InvalidArgumentException::class);

        $this->resolveOrderPaymentStateListener->__invoke(new CompletedEvent($payment, new Marking()));
    }

    public function testResolvesOrderPaymentState(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $payment->expects($this->once())->method('getOrder')->willReturn($order);
        $this->stateResolver->expects($this->once())->method('resolve')->with($order);

        $this->resolveOrderPaymentStateListener->__invoke(new CompletedEvent($payment, new Marking()));
    }
}
