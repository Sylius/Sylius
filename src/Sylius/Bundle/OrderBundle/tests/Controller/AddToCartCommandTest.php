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

namespace Tests\Sylius\Bundle\OrderBundle\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToCartCommandTest extends TestCase
{
    /** @var OrderInterface&MockObject */
    private OrderInterface $order;

    /** @var OrderItemInterface&MockObject */
    private OrderItemInterface $orderItem;

    private AddToCartCommand $addToCartCommand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->addToCartCommand = new AddToCartCommand($this->order, $this->orderItem);
    }

    public function testAddCartItemCommand(): void
    {
        self::assertInstanceOf(AddToCartCommandInterface::class, $this->addToCartCommand);
    }

    public function testHasOrder(): void
    {
        self::assertSame($this->order, $this->addToCartCommand->getCart());
    }

    public function testHasOrderItem(): void
    {
        self::assertSame($this->orderItem, $this->addToCartCommand->getCartItem());
    }
}
