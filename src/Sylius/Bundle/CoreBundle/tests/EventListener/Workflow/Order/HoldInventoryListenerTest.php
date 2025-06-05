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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\HoldInventoryListener;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class HoldInventoryListenerTest extends TestCase
{
    private MockObject&OrderInventoryOperatorInterface $orderInventoryOperator;

    private HoldInventoryListener $listener;

    protected function setUp(): void
    {
        $this->orderInventoryOperator = $this->createMock(OrderInventoryOperatorInterface::class);
        $this->listener = new HoldInventoryListener($this->orderInventoryOperator);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItHoldsOrderInventory(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderInventoryOperator
            ->expects($this->once())
            ->method('hold')
            ->with($order)
        ;

        ($this->listener)($event);
    }
}
