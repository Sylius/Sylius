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

namespace Tests\Sylius\Bundle\OrderBundle\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToCartCommandFactoryTest extends TestCase
{
    private AddToCartCommandFactory $addToCartCommandFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addToCartCommandFactory = new AddToCartCommandFactory();
    }

    public function testAddToCartCommandFactory(): void
    {
        self::assertInstanceOf(AddToCartCommandFactoryInterface::class, $this->addToCartCommandFactory);
    }

    public function testCreatesAddToCartCommandWithCartAndCartItem(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface&MockObject $cartItem */
        $cartItem = $this->createMock(OrderItemInterface::class);

        $command = $this->addToCartCommandFactory->createWithCartAndCartItem($cart, $cartItem);

        self::assertInstanceOf(AddToCartCommand::class, $command);
        self::assertSame($cart, $command->getCart());
        self::assertSame($cartItem, $command->getCartItem());
    }
}
