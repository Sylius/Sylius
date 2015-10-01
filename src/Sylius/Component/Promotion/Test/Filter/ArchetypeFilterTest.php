<?php

namespace Sylius\Component\Promotion\Test\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Promotion\Filter\ArchetypeFilter;
use Sylius\Component\Archetype\Model\ArchetypeInterface;

class ArchetypeFilterTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_ARCHETYPE = 123;

    /**
     * Subject under tests
     *
     * @var ArchetypeFilter
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new ArchetypeFilter([
            ArchetypeFilter::OPTION_ARCHETYPE => self::EXPECTED_ARCHETYPE,
        ]);
    }

    /**
     * @test
     */
    public function it_returns_collection_with_item_if_item_is_having_same_archetype()
    {
        $item = $this->mockOrderItemWithArchetypeId(self::EXPECTED_ARCHETYPE);

        $collection = new ArrayCollection([
            $item,
        ]);

        $result = $this->sut->apply($collection);

        $this->assertEquals(
            new ArrayCollection([$item]),
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
    public function it_returns_multiple_items_if_more_than_one_items_are_having_requested_archetype()
    {
        $expected = $collection = new ArrayCollection([
            $item1 = $this->mockOrderItemWithArchetypeId(self::EXPECTED_ARCHETYPE),
            $item2 = $this->mockOrderItemWithArchetypeId(self::EXPECTED_ARCHETYPE),
        ]);

        $result = $this->sut->apply($collection);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_returns_only_items_with_requested_archetype()
    {
        $collection = new ArrayCollection([
            $item1 = $this->mockOrderItemWithArchetypeId(self::EXPECTED_ARCHETYPE),
            $this->mockOrderItemWithArchetypeId(12345),
            $item2 = $this->mockOrderItemWithArchetypeId(self::EXPECTED_ARCHETYPE),
            $this->mockOrderItemWithArchetypeId(1234),
        ]);

        $result = $this->sut->apply($collection);

        $expected = new ArrayCollection([
            $item1, $item2,
        ]);

        $this->assertCount(2, $result);
        $this->assertEquals($expected, $result);
    }

    ###################################################################

    private function mockOrderItemWithArchetypeId($archetypeId)
    {
        $orderItem = $this->getMock(OrderItemInterface::class);
        $product = $this->getMock(ProductInterface::class);
        $archetype = $this->getMock(ArchetypeInterface::class);

        $orderItem->method('getProduct')->willReturn($product);
        $product->method('getArchetype')->willReturn($archetype);
        $archetype->method('getId')->willReturn($archetypeId);

        return $orderItem;
    }
}
