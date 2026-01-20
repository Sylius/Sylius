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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\CartItemFactory;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class CartItemFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private MockObject&ProductVariantResolverInterface $variantResolver;

    private MockObject&OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private MockObject&OrderItemInterface $cartItem;

    private CartItemFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->variantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->cartItem = $this->createMock(OrderItemInterface::class);
        $this->factory = new CartItemFactory(
            $this->decoratedFactory,
            $this->variantResolver,
            $this->orderItemQuantityModifier,
        );
    }

    public function testShouldImplementCartItemFactoryInterface(): void
    {
        $this->assertInstanceOf(CartItemFactoryInterface::class, $this->factory);
    }

    public function testShouldBeResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testShouldCreateNewCartItem(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->cartItem);

        $this->assertSame($this->cartItem, $this->factory->createNew());
    }

    public function testShouldCreateCartItemAndAssignProductVariant(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->cartItem);
        $this->variantResolver->expects($this->once())->method('getVariant')->with($product)->willReturn($productVariant);
        $this->cartItem->expects($this->once())->method('setVariant')->with($productVariant);

        $this->orderItemQuantityModifier->expects($this->once())->method('modify')->with($this->cartItem, 1);

        $this->assertSame($this->cartItem, $this->factory->createForProduct($product));
    }

    public function testShouldCreateCartItemForGivenCart(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->cartItem);
        $this->cartItem->expects($this->once())->method('setOrder')->with($order);

        $this->assertSame($this->cartItem, $this->factory->createForCart($order));
    }
}
