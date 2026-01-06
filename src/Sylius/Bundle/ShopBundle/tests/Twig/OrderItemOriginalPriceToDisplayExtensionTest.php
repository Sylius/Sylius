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

namespace Tests\Sylius\Bundle\ShopBundle\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Twig\OrderItemOriginalPriceToDisplayExtension;
use Sylius\Component\Core\Model\OrderItem;
use Twig\Extension\AbstractExtension;

final class OrderItemOriginalPriceToDisplayExtensionTest extends TestCase
{
    private OrderItemOriginalPriceToDisplayExtension $orderItemOriginalPriceToDisplayExtension;

    protected function setUp(): void
    {
        $this->orderItemOriginalPriceToDisplayExtension = new OrderItemOriginalPriceToDisplayExtension();
    }

    public function testImplementsATwigAbstractExtension(): void
    {
        $this->assertInstanceOf(AbstractExtension::class, $this->orderItemOriginalPriceToDisplayExtension);
    }

    public function testReturnsAnOriginalUnitPriceIfItIsGreaterThanOtherPrices(): void
    {
        /** @var OrderItem&MockObject $item */
        $item = $this->createMock(OrderItem::class);

        $item->expects($this->once())->method('getUnitPrice')->willReturn(1000);
        $item->expects($this->once())->method('getDiscountedUnitPrice')->willReturn(800);
        $item->expects($this->once())->method('getOriginalUnitPrice')->willReturn(5000);

        $this->assertSame(5000, $this->orderItemOriginalPriceToDisplayExtension->getOriginalPriceToDisplay($item));
    }

    public function testReturnsAnUnitPriceIfItIsGreaterThanOriginalUnitPriceAndDiscountedUnitPrice(): void
    {
        /** @var OrderItem&MockObject $item */
        $item = $this->createMock(OrderItem::class);

        $item->expects($this->once())->method('getUnitPrice')->willReturn(1000);
        $item->expects($this->once())->method('getDiscountedUnitPrice')->willReturn(800);
        $item->expects($this->once())->method('getOriginalUnitPrice')->willReturn(null);

        $this->assertSame(1000, $this->orderItemOriginalPriceToDisplayExtension->getOriginalPriceToDisplay($item));
    }
}
