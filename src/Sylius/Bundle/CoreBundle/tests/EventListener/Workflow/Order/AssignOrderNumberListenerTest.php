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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderNumberListener;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class AssignOrderNumberListenerTest extends TestCase
{
    private MockObject&OrderNumberAssignerInterface $orderNumberAssigner;

    private AssignOrderNumberListener $listener;

    protected function setUp(): void
    {
        $this->orderNumberAssigner = $this->createMock(OrderNumberAssignerInterface::class);
        $this->listener = new AssignOrderNumberListener($this->orderNumberAssigner);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new TransitionEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItAssignsOrderNumber(): void
    {
        $order = new Order();
        $event = new TransitionEvent($order, new Marking());

        $this->orderNumberAssigner
            ->expects($this->once())
            ->method('assignNumber')
            ->with($order)
        ;

        ($this->listener)($event);
    }
}
