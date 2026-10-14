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

namespace Tests\Sylius\Bundle\OrderBundle\Adder;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Adder\CartItemAdder;
use Sylius\Bundle\OrderBundle\Adder\CartItemAdderInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

#[AllowMockObjectsWithoutExpectations]
final class CartItemAdderTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;

    private MockObject&ObjectManager $orderManager;

    private CartItemAdder $cartItemAdder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->orderManager = $this->createMock(ObjectManager::class);
        $this->cartItemAdder = new CartItemAdder($this->eventDispatcher, $this->orderManager);
    }

    public function testItImplementsCartItemAdderInterface(): void
    {
        self::assertInstanceOf(CartItemAdderInterface::class, $this->cartItemAdder);
    }

    public function testAddsCartItemToCartAndPersistsTheCart(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var AddToCartCommandInterface&MockObject $addToCartCommand */
        $addToCartCommand = $this->createMock(AddToCartCommandInterface::class);
        $addToCartCommand->method('getCart')->willReturn($cart);

        $dispatchedEventNames = [];

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (GenericEvent $event, string $eventName) use (&$dispatchedEventNames, $addToCartCommand) {
                self::assertSame($addToCartCommand, $event->getSubject());
                $dispatchedEventNames[] = $eventName;

                return $event;
            })
        ;

        $this->orderManager->expects(self::once())->method('persist')->with($cart);
        $this->orderManager->expects(self::once())->method('flush');

        $this->cartItemAdder->add($addToCartCommand);

        self::assertSame(
            [SyliusCartEvents::CART_ITEM_ADD, SyliusCartEvents::CART_ITEM_POST_ADD],
            $dispatchedEventNames,
        );
    }
}
