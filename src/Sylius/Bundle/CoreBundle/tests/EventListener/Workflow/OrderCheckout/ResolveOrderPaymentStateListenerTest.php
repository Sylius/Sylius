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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderPaymentStateListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderPaymentStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $orderPaymentStateResolver;

    private ResolveOrderPaymentStateListener $listener;

    protected function setUp(): void
    {
        $this->orderPaymentStateResolver = $this->createMock(StateResolverInterface::class);
        $this->listener = new ResolveOrderPaymentStateListener($this->orderPaymentStateResolver);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItResolvesOrderPaymentStateAfterComplete(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderPaymentStateResolver->expects($this->once())->method('resolve')->with($order);

        ($this->listener)($event);
    }
}
