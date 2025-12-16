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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Core\Promotion\Filter\TaxonFilter;

final class TaxonFilterTest extends TestCase
{
    private MockObject&OrderItemInterface $item;

    private TaxonFilter $filter;

    protected function setUp(): void
    {
        $this->item = $this->createMock(OrderItemInterface::class);
        $this->filter = new TaxonFilter();
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
        $firstTaxon = $this->createMock(TaxonInterface::class);
        $secondTaxon = $this->createMock(TaxonInterface::class);
        $this->item->expects($this->once())->method('getProduct')->willReturn($firstProduct);
        $firstProduct->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$firstTaxon]));
        $firstTaxon->expects($this->once())->method('getCode')->willReturn('taxon1');
        $secondItem->expects($this->once())->method('getProduct')->willReturn($secondProduct);
        $secondProduct->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$secondTaxon]));
        $secondTaxon->expects($this->once())->method('getCode')->willReturn('taxon2');

        $this->assertEquals(
            [$this->item],
            $this->filter->filter([$this->item, $secondItem], ['filters' => ['taxons_filter' => ['taxons' => ['taxon1']]]]),
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
            $this->filter->filter([$this->item], ['filters' => ['taxons_filter' => ['taxons' => []]]]),
        );
    }
}
