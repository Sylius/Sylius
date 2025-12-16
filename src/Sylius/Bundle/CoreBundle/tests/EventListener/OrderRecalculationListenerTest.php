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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\CoreBundle\EventListener\OrderRecalculationListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderRecalculationListenerTest extends TestCase
{
    private MockObject&OrderProcessorInterface $orderProcessor;

    private OrderRecalculationListener $orderRecalculationListener;

    protected function setUp(): void
    {
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->orderRecalculationListener = new OrderRecalculationListener($this->orderProcessor);
    }

    public function testUsesOrderProcessorToRecalculateOrder(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $this->orderProcessor->expects($this->once())->method('process')->with($order);

        $this->orderRecalculationListener->recalculateOrder($event);
    }

    public function testThrowsExceptionIfEventSubjectIsNotOrder(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new stdClass());

        $this->expectException(InvalidArgumentException::class);

        $this->orderRecalculationListener->recalculateOrder($event);
    }
}
