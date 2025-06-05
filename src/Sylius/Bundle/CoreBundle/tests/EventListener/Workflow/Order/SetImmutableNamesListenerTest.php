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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SetImmutableNamesListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class SetImmutableNamesListenerTest extends TestCase
{
    private MockObject&OrderItemNamesSetterInterface $orderItemNamesSetter;

    private SetImmutableNamesListener $listener;

    protected function setUp(): void
    {
        $this->orderItemNamesSetter = $this->createMock(OrderItemNamesSetterInterface::class);
        $this->listener = new SetImmutableNamesListener($this->orderItemNamesSetter);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItSetsImmutableNames(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderItemNamesSetter->expects($this->once())->method('__invoke')->with($order);

        ($this->listener)($event);
    }
}
