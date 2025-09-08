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
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\ShopBundle\EventListener\CartItemAddListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartItemAddListenerTest extends TestCase
{
    private MockObject&OrderModifierInterface $orderModifier;

    private CartItemAddListener $cartItemAddListener;

    protected function setUp(): void
    {
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);

        $this->cartItemAddListener = new CartItemAddListener($this->orderModifier);
    }

    public function testAddsCartItemToOrder(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var AddToCartCommandInterface&MockObject $addToCartCommand */
        $addToCartCommand = $this->createMock(AddToCartCommandInterface::class);
        /** @var OrderItemInterface&MockObject $orderItem */
        $orderItem = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $addToCartCommand->expects($this->once())->method('getCart')->willReturn($order);
        $addToCartCommand->expects($this->once())->method('getCartItem')->willReturn($orderItem);
        $event->expects($this->once())->method('getSubject')->willReturn($addToCartCommand);
        $this->orderModifier->expects($this->once())->method('addToOrder')->with($order, $orderItem);

        $this->cartItemAddListener->addToOrder($event);
    }

    public function testThrowsExceptionIfEventSubjectIsNotAddToCartCommand(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->cartItemAddListener->addToOrder($event);
    }
}
