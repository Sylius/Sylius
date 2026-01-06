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

namespace Tests\Sylius\Component\Core\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetter;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

final class OrderItemNamesSetterTest extends TestCase
{
    private OrderItemNamesSetter $orderItemNamesSetter;

    protected function setUp(): void
    {
        $this->orderItemNamesSetter = new OrderItemNamesSetter();
    }

    public function testShouldImplementOrderItemNamesSetterInterface(): void
    {
        $this->assertInstanceOf(OrderItemNamesSetterInterface::class, $this->orderItemNamesSetter);
    }

    public function testShouldSetsProductAndProductVariantNamesOnOrderItems(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $variantTranslation = $this->createMock(ProductVariantTranslationInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $productTranslation = $this->createMock(ProductTranslationInterface::class);

        $order->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$orderItem]));
        $orderItem->expects($this->once())->method('getVariant')->willReturn($variant);
        $variant->expects($this->exactly(2))->method('getProduct')->willReturn($product);
        $variant->expects($this->once())->method('getTranslation')->with('en_US')->willReturn($variantTranslation);
        $variantTranslation->expects($this->once())->method('getName')->willReturn('Variant name');
        $product->expects($this->once())->method('getTranslation')->with('en_US')->willReturn($productTranslation);
        $productTranslation->expects($this->once())->method('getName')->willReturn('Product name');
        $orderItem->expects($this->once())->method('setVariantName')->with('Variant name');
        $orderItem->expects($this->once())->method('setProductName')->with('Product name');

        $this->orderItemNamesSetter->__invoke($order);
    }
}
