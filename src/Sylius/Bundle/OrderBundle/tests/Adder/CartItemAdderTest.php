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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Adder\CartItemAdder;
use Sylius\Bundle\OrderBundle\Adder\CartItemAdderInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartItemAdderTest extends TestCase
{
    private AddToCartCommandFactoryInterface&MockObject $addToCartCommandFactory;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private MockObject&ObjectManager $orderManager;

    private CartItemAdder $cartItemAdder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addToCartCommandFactory = $this->createMock(AddToCartCommandFactoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->orderManager = $this->createMock(ObjectManager::class);
        $this->cartItemAdder = new CartItemAdder(
            $this->addToCartCommandFactory,
            $this->eventDispatcher,
            $this->orderManager,
        );
    }

    public function testItImplementsCartItemAdderInterface(): void
    {
        self::assertInstanceOf(CartItemAdderInterface::class, $this->cartItemAdder);
    }

    public function testAddsCartItemToCartAndPersistsTheCart(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface&MockObject $cartItem */
        $cartItem = $this->createMock(OrderItemInterface::class);
        /** @var AddToCartCommandInterface&MockObject $addToCartCommand */
        $addToCartCommand = $this->createMock(AddToCartCommandInterface::class);

        $this->addToCartCommandFactory
            ->expects(self::once())
            ->method('createWithCartAndCartItem')
            ->with($cart, $cartItem)
            ->willReturn($addToCartCommand)
        ;

        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::callback(
                    static fn (GenericEvent $event): bool => $event->getSubject() === $addToCartCommand,
                ),
                SyliusCartEvents::CART_ITEM_ADD,
            )
            ->willReturnArgument(0)
        ;

        $this->orderManager->expects(self::once())->method('persist')->with($cart);
        $this->orderManager->expects(self::once())->method('flush');

        $this->cartItemAdder->add($cart, $cartItem);
    }
}
