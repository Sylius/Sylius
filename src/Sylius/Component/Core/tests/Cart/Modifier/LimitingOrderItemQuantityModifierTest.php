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

namespace Tests\Sylius\Component\Core\Cart\Modifier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Cart\Modifier\LimitingOrderItemQuantityModifier;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

final class LimitingOrderItemQuantityModifierTest extends TestCase
{
    private MockObject&OrderItemQuantityModifierInterface $itemQuantityModifier;

    private LimitingOrderItemQuantityModifier $modifier;

    protected function setUp(): void
    {
        $this->itemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->modifier = new LimitingOrderItemQuantityModifier($this->itemQuantityModifier, 1000);
    }

    public function testShouldImplementOrderItemModifierInterface(): void
    {
        $this->assertInstanceOf(OrderItemQuantityModifierInterface::class, $this->modifier);
    }

    public function testShouldRestrictMaxItemQuantityToTheStatedLimit(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);

        $this->itemQuantityModifier->expects($this->once())->method('modify')->with($orderItem, 1000);

        $this->modifier->modify($orderItem, 9999);
    }
}
