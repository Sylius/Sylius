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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ProcessOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ProcessOrderListenerTest extends TestCase
{
    private MockObject&OrderProcessorInterface $orderProcessor;

    private ProcessOrderListener $processOrderListener;

    protected function setUp(): void
    {
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->processOrderListener = new ProcessOrderListener($this->orderProcessor);
    }

    public function testThrowsExceptionWhenEventSubjectIsNotAPayment(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processOrderListener->__invoke(new CompletedEvent(new stdClass(), new Marking()));
    }

    public function testThrowsExceptionWhenEventPaymentHasNoOrder(): void
    {
        $payment = $this->createMock(PaymentInterface::class);

        $payment->expects($this->once())->method('getOrder')->willReturn(null);

        $this->expectException(InvalidArgumentException::class);

        $this->processOrderListener->__invoke(new CompletedEvent($payment, new Marking()));
    }

    public function testProcessesOrder(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $payment->expects($this->once())->method('getOrder')->willReturn($order);
        $this->orderProcessor->expects($this->once())->method('process')->with($order);

        $this->processOrderListener->__invoke(new CompletedEvent($payment, new Marking()));
    }
}
