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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderCustomerIpListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\Event\Event;

final class OrderCustomerIpListenerTest extends TestCase
{
    private IpAssignerInterface&MockObject $ipAssigner;

    private MockObject&RequestStack $requestStack;

    private OrderCustomerIpListener $orderCustomerIpListener;

    protected function setUp(): void
    {
        $this->ipAssigner = $this->createMock(IpAssignerInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->orderCustomerIpListener = new OrderCustomerIpListener($this->ipAssigner, $this->requestStack);
    }

    public function testUsesAssignerToAssignCustomerIpToOrder(): void
    {
        /** @var Event&MockObject $event */
        $event = $this->createMock(Event::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn($request);
        $this->ipAssigner->expects($this->once())->method('assign')->with($order, $request);

        ($this->orderCustomerIpListener)($event);
    }

    public function testThrowsExceptionIfEventSubjectIsNotOrder(): void
    {
        /** @var Event&MockObject $event */
        $event = $this->createMock(Event::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        ($this->orderCustomerIpListener)($event);
    }

    public function testDoesNothingIfRequestIsNotAvailable(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var Event&MockObject $event */
        $event = $this->createMock(Event::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);
        $this->ipAssigner->expects($this->never())->method('assign')->with($order, $request);

        ($this->orderCustomerIpListener)($event);
    }
}
