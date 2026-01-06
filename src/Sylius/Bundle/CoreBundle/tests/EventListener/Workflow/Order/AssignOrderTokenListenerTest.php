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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderTokenListener;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class AssignOrderTokenListenerTest extends TestCase
{
    private MockObject&OrderTokenAssignerInterface $orderTokenAssigner;

    private AssignOrderTokenListener $listener;

    protected function setUp(): void
    {
        $this->orderTokenAssigner = $this->createMock(OrderTokenAssignerInterface::class);
        $this->listener = new AssignOrderTokenListener($this->orderTokenAssigner);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new TransitionEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItAssignsOrderToken(): void
    {
        $order = new Order();
        $event = new TransitionEvent($order, new Marking());

        $this->orderTokenAssigner
            ->expects($this->once())
            ->method('assignTokenValueIfNotSet')
            ->with($order)
        ;

        ($this->listener)($event);
    }
}
