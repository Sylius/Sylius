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

namespace Tests\Sylius\Component\Core\Promotion\Filter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Filter\ProductFilter;

final class ProductFilterTest extends TestCase
{
    private MockObject&OrderItemInterface $item;

    private ProductFilter $filter;

    protected function setUp(): void
    {
        $this->item = $this->createMock(OrderItemInterface::class);
        $this->filter = new ProductFilter();
    }

    public function testImplementFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testShouldFilterPassedOrderItemsWithGivenConfiguration(): void
    {
        $secondItem = $this->createMock(OrderItemInterface::class);
        $firstProduct = $this->createMock(ProductInterface::class);
        $secondProduct = $this->createMock(ProductInterface::class);
        $this->item->expects($this->once())->method('getProduct')->willReturn($firstProduct);
        $firstProduct->expects($this->once())->method('getCode')->willReturn('product1');
        $secondItem->expects($this->once())->method('getProduct')->willReturn($secondProduct);
        $secondProduct->expects($this->once())->method('getCode')->willReturn('product2');

        $this->assertEquals(
            [$this->item],
            $this->filter->filter([$this->item, $secondItem], ['filters' => ['products_filter' => ['products' => ['product1']]]]),
        );
    }

    public function testShouldReturnAllItemsIfConfigurationIsInvalid(): void
    {
        $this->assertEquals([$this->item], $this->filter->filter([$this->item], []));
    }

    public function testShouldReturnAllItemsIfConfigurationIsEmpty(): void
    {
        $this->assertEquals(
            [$this->item],
            $this->filter->filter([$this->item], ['filters' => ['products_filter' => ['products' => []]]]),
        );
    }
}
