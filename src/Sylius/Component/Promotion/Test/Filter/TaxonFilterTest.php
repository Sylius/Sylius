<?php

namespace Sylius\Component\Promotion\Test\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Promotion\Filter\TaxonFilter;

class TaxonFilterTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_TAXON = 123;

    /**
     * Subject under tests
     *
     * @var TaxonFilter
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new TaxonFilter([
            TaxonFilter::OPTION_TAXON => self::EXPECTED_TAXON,
        ]);
    }

    /**
     * @test
     */
    public function it_returns_collection_with_item_if_item_is_having_same_taxon()
    {
        $item1 = $this->mockOrderItemWithTaxonId(self::EXPECTED_TAXON);

        $collection = new ArrayCollection([
            $item1,
        ]);

        $result = $this->sut->apply($collection);

        $this->assertEquals(
            new ArrayCollection([$item1]),
            $result
        );
    }

    /**
     * @test
     */
    public function it_returns_empty_collection_if_empty_collection_given()
    {
        $collection = new ArrayCollection();

        $result = $this->sut->apply($collection);
        $this->assertEquals($collection, $result);
    }

    /**
     * @test
     */
    public function it_returns_multiple_items_if_more_than_one_items_are_having_requested_taxon()
    {
        $expected = $collection = new ArrayCollection([
            $item1 = $this->mockOrderItemWithTaxonId(self::EXPECTED_TAXON),
            $item2 = $this->mockOrderItemWithTaxonId(self::EXPECTED_TAXON),
        ]);

        $result = $this->sut->apply($collection);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_returns_only_items_with_requested_taxon()
    {
        $collection = new ArrayCollection([
            $item1 = $this->mockOrderItemWithTaxonId(self::EXPECTED_TAXON),
            $this->mockOrderItemWithTaxonId(12345),
            $item2 = $this->mockOrderItemWithTaxonId(self::EXPECTED_TAXON),
            $this->mockOrderItemWithTaxonId(1234),
        ]);

        $result = $this->sut->apply($collection);

        $expected = new ArrayCollection([
            $item1, $item2,
        ]);

        $this->assertCount(2, $result);
        $this->assertEquals($expected, $result);
    }

    ###################################################################

    private function mockOrderItemWithTaxonId($taxonId)
    {
        $orderItem = $this->getMock(OrderItemInterface::class);
        $product = $this->getMock(ProductInterface::class);
        $taxon = $this->getMock(Taxon::class);
        $orderItem->method('getProduct')->willReturn($product);
        $product->method('getTaxons')->willReturn([$taxon]);

        $taxon->method('getId')->willReturn($taxonId);

        return $orderItem;
    }
}
