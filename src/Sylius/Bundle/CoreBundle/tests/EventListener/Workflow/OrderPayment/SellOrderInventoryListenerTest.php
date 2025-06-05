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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\SellOrderInventoryListener;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class SellOrderInventoryListenerTest extends TestCase
{
    private MockObject&OrderInventoryOperatorInterface $orderInventoryOperator;

    private SellOrderInventoryListener $listener;

    protected function setUp(): void
    {
        $this->orderInventoryOperator = $this->createMock(OrderInventoryOperatorInterface::class);
        $this->listener = new SellOrderInventoryListener($this->orderInventoryOperator);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $subject = new stdClass();
        $event = new CompletedEvent($subject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItSellsOrderInventory(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderInventoryOperator->expects($this->once())->method('sell')->with($order);

        ($this->listener)($event);
    }
}
