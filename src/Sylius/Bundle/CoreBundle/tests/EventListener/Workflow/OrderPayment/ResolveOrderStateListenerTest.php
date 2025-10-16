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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\ResolveOrderStateListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $orderStateResolver;

    private ResolveOrderStateListener $listener;

    protected function setUp(): void
    {
        $this->orderStateResolver = $this->createMock(StateResolverInterface::class);
        $this->listener = new ResolveOrderStateListener($this->orderStateResolver);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $subject = new stdClass();
        $event = new CompletedEvent($subject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItResolvesOrderState(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderStateResolver->expects($this->once())->method('resolve')->with($order);

        ($this->listener)($event);
    }
}
