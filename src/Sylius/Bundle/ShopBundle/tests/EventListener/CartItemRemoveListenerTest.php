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
use Sylius\Bundle\ShopBundle\EventListener\CartItemRemoveListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartItemRemoveListenerTest extends TestCase
{
    private MockObject&OrderModifierInterface $orderModifier;

    private CartItemRemoveListener $cartItemRemoveListener;

    protected function setUp(): void
    {
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);

        $this->cartItemRemoveListener = new CartItemRemoveListener($this->orderModifier);
    }

    public function testRemovesOrderItemFromOrder(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var OrderItemInterface&MockObject $orderItem */
        $orderItem = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $orderItem->expects($this->once())->method('getOrder')->willReturn($order);
        $event->expects($this->once())->method('getSubject')->willReturn($orderItem);
        $this->orderModifier->expects($this->once())->method('removeFromOrder')->with($order, $orderItem);

        $this->cartItemRemoveListener->removeFromOrder($event);
    }

    public function testThrowsExceptionIfEventSubjectIsNotOrderItem(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->cartItemRemoveListener->removeFromOrder($event);
    }
}
