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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderCheckoutStateListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderCheckoutStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $orderCheckoutStateResolver;

    private ResolveOrderCheckoutStateListener $listener;

    protected function setUp(): void
    {
        $this->orderCheckoutStateResolver = $this->createMock(StateResolverInterface::class);
        $this->listener = new ResolveOrderCheckoutStateListener($this->orderCheckoutStateResolver);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItResolvesOrderCheckoutStateAfterSelectShipping(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderCheckoutStateResolver->expects($this->once())->method('resolve')->with($order);

        ($this->listener)($event);
    }
}
