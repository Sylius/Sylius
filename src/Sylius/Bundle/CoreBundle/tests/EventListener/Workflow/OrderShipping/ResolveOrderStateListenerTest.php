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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping\ResolveOrderStateListener;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $stateResolver;

    private ResolveOrderStateListener $resolveOrderStateListener;

    protected function setUp(): void
    {
        $this->stateResolver = $this->createMock(StateResolverInterface::class);
        $this->resolveOrderStateListener = new ResolveOrderStateListener($this->stateResolver);
    }

    public function testThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $callback = $this->createMock(stdClass::class);
        $event = new CompletedEvent($callback, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->resolveOrderStateListener)($event);
    }

    public function testResolvesOrderStateAfterOrderBeingShipped(): void
    {
        $order = new Order();
        $event = new CompletedEvent($order, new Marking());

        $this->stateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($order)
        ;

        ($this->resolveOrderStateListener)($event);
    }
}
